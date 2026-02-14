// js/dashboard_charts.js

const DashboardCharts = {
    userId: null, 
    currentRange: '3m',

    init: function() {
        this.cacheDOM();
        this.bindEvents();
        this.fetchData('3m'); // Default
    },

    cacheDOM: function() {
        this.toggles = document.querySelectorAll('.ToggleBtn');
        
        // Weight Elements
        this.pathWeight = document.getElementById('pathWeight');
        this.dotWeight = document.getElementById('dotWeight');
        this.valWeightNow = document.getElementById('valWeightNow');
        this.lblWeightChange = document.getElementById('lblWeightChange');

        // Fat Elements
        this.pathFat = document.getElementById('pathFat');
        this.dotFat = document.getElementById('dotFat');
        this.valFatNow = document.getElementById('valFatNow');
        this.lblFatChange = document.getElementById('lblFatChange');
    },

    bindEvents: function() {
        this.toggles.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // UI Toggle: Strict Single Selection
                this.toggles.forEach(b => b.classList.remove('active', 'Active')); // Remove both to be safe
                e.target.classList.add('active', 'Active'); 

                const range = e.target.dataset.range;
                this.fetchData(range);
            });
        });
    },

    fetchData: function(range) {
        // Simple Loading Indicator
        this.valWeightNow.style.opacity = '0.5';
        this.valFatNow.style.opacity = '0.5';

        fetch(`../../APIs/GetStats.php?range=${range}`)
            .then(res => res.json())
            .then(res => {
                this.valWeightNow.style.opacity = '1';
                this.valFatNow.style.opacity = '1';
                
                if(res.success) {
                    this.renderCharts(res.data);
                } else {
                    console.error('Stats Error:', res.error);
                }
            })
            .catch(err => console.error('Fetch Error:', err));
    },

    renderCharts: function(data) {
        if (!data || data.length === 0) return;

        // Process Data
        const weights = data.map(d => parseFloat(d.Weight));
        const fats = data.map(d => parseFloat(d.BodyFat));
        
        this.updateChart('weight', weights, this.pathWeight, this.dotWeight, this.valWeightNow, this.lblWeightChange);
        this.updateChart('fat', fats, this.pathFat, this.dotFat, this.valFatNow, this.lblFatChange);
    },

    updateChart: function(type, values, pathEl, dotEl, valEl, changeEl) {
        if(values.length === 0) return;

        const currentVal = values[values.length - 1];
        const prevVal = values[0];
        const change = currentVal - prevVal;

        // Update Text
        valEl.textContent = currentVal;
        
        // Update Change Label
        changeEl.className = 'Change'; 
        if (change > 0) {
            changeEl.textContent = '+' + change.toFixed(1) + (type==='weight'?'KG':'%');
            changeEl.classList.add('positive'); // Default positive for gain
             if(type === 'weight') changeEl.classList.replace('positive', 'negative'); // Weight gain is 'negative' (red)
        } else {
            changeEl.textContent = change.toFixed(1) + (type==='weight'?'KG':'%');
            changeEl.classList.add('negative'); // Default negative for loss
            if(type === 'weight' || type === 'fat') changeEl.classList.replace('negative', 'positive'); // Weight/Fat loss is 'positive' (green)
        }

        // Generate SVG Path
        const min = Math.min(...values);
        const max = Math.max(...values);
        let range = max - min;
        
        const points = values.map((val, idx) => {
            const x = (idx / (values.length - 1)) * 100;
            let y;
            if (range === 0) {
                y = 20; // Center if flat
            } else {
                const normalized = (val - min) / range;
                 y = 35 - (normalized * 30); 
            }
            return [x, y];
        });

        // Create Path String
        let d = '';
        if (points.length === 1) {
            d = `M0,${points[0][1]} L100,${points[0][1]}`; 
        } else {
            d = `M${points[0][0]},${points[0][1]}`;
            for(let i=1; i<points.length; i++) {
                d += ` L${points[i][0]},${points[i][1]}`; // Linear
            }
        }
        
        pathEl.setAttribute('d', d);

        // Update HTML Dot Position
        const lastPt = points[points.length - 1];
        // Convert SVG coordinates to CSS %
        // SVG Y=0 is 0%, Y=40 is 100%
        const topPct = (lastPt[1] / 40) * 100;
        
        dotEl.style.left = lastPt[0] + '%';
        dotEl.style.top = topPct + '%';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    DashboardCharts.init();
});
