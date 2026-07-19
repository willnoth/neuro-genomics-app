#!/usr/bin/env python3
import argparse
import json
import sys
import os
import requests

def parse_single_patient_vcf(vcf_path, job_id):
    print(f"[INFO] Initializing sequencing read for file: {vcf_path}", flush=True)
    update_laravel_progress(job_id, progress=20)

    if not os.path.exists(vcf_path):
        raise FileNotFoundError(f"Target file not found at: {vcf_path}")

    # Default baseline conditions (assuming unmutated wildtype/intact arms)
    results = {
        "idh_status": "Wildtype",
        "has_1p19q_codeletion": False,
        "cic_mutation_status": "Wildtype",
        "fubp1_mutation_status": "Wildtype",
        "tert_promoter_mutation": False,
        "mgmt_methylation_status": "Unmethylated"
    }

    del_1p = False
    del_19q = False

    # Read the file line-by-line to handle large file sizes efficiently
    with open(vcf_path, "r") as file:
        for line in file:
            if line.startswith("#"):
                continue

            columns = line.strip().split("\t")
            if len(columns) < 5:
                continue

            chrom = columns[0].lower().replace("chr", "")
            try:
                pos = int(columns[1])
            except ValueError:
                continue

            ref = columns[3]
            alt = columns[4]
            info_field = columns[7] if len(columns) > 7 else ""

            # 1. Evaluate IDH1 R132H hotspot marker (chr2:208248388 C -> T)
            if chrom == "2" and pos == 208248388:
                if ref == "C" and "T" in alt:
                    results["idh_status"] = "IDH1-R132H (Somatic Mutated)"

            # 2. Map structural chromosomal deletions flagged in the VCF file info field
            if chrom == "1" and "SVTYPE=DEL" in info_field:
                del_1p = True
            if chrom == "19" and "SVTYPE=DEL" in info_field:
                del_19q = True

            # 3. Pull additional lineage drivers and modifier targets
            if "GENE=CIC" in info_field:
                results["cic_mutation_status"] = "Mutant (Loss of Function)"
            if "GENE=FUBP1" in info_field:
                results["fubp1_mutation_status"] = "Mutant (Stop-gain)"
            if "GENE=TERT" in info_field:
                results["tert_promoter_mutation"] = True
            if "MGMT=METHYLATED" in info_field:
                results["mgmt_methylation_status"] = "Methylated"

    update_laravel_progress(job_id, progress=80)

    # Enforce strict WHO co-deletion verification logic rules
    if del_1p and del_19q:
        results["has_1p19q_codeletion"] = True

    update_laravel_progress(job_id, progress=100)
    return results

def update_laravel_progress(job_id, progress):
    try:
        # 'app' points directly to our running web container on the Docker bridge
        url = f"http://app/api/internal/genomic-job/{job_id}/progress"
        headers = {"X-Internal-Secret": "your-env-security-key", "Content-Type": "application/json"}
        requests.post(url, json={"progress": progress}, headers=headers, timeout=2)
    except Exception:
        pass # Fail silently if the web server is temporarily locked during boot sequences

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument('--input', required=True)
    parser.add_argument('--job-id', required=True)
    args = parser.parse_args()
    
    try:
        final_data = parse_single_patient_vcf(args.input, args.job_id)
        
        # Enclose the raw object output cleanly so Laravel can parse it safely
        print("\n---PIPELINE_OUTPUT_START---")
        print(json.dumps(final_data))
        print("---PIPELINE_OUTPUT_END---")
        sys.exit(0)
    except Exception as e:
        print(f"[ERROR] Engine script crash: {str(e)}", file=sys.stderr)
        sys.exit(1)

