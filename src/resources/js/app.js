import { 
    Chart, 
    ScatterController, 
    PointElement, 
    LineElement, 
    LinearScale, 
    LogarithmicScale, 
    Legend, 
    Title, 
    Tooltip 
} from 'chart.js';

Chart.register(
    ScatterController, 
    PointElement, 
    LineElement, 
    LinearScale, 
    LogarithmicScale, 
    Legend, 
    Title, 
    Tooltip
);

window.Chart = Chart;

