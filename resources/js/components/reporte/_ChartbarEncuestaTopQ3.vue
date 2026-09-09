<script>
import { Bar } from 'vue-chartjs';
import 'chartjs-plugin-labels';
import { bgColors, bgBorder } from './../../colorreport';

export default {
    extends: Bar,
    props: {
        datos: {
            type: Object,
            default: () => ({}),
        },
        labelMap: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            bgColors,
            bgBorder,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0,
                            callback: function (value) { return Number.isInteger(value) ? value : ''; },
                            beginAtZero: true,
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Cantidad',
                        },
                    }],
                },
                plugins: {
                    labels: {
                        render: 'value',
                        fontColor: ['white', 'black'],
                    },
                },
                legend: {
                    display: false,
                },
            },
        };
    },
    methods: {
        resolveLabel(value) {
            if (Object.prototype.hasOwnProperty.call(this.labelMap, value)) {
                return this.labelMap[value];
            }
            return value;
        },
        mostrar() {
            const entries = Object.entries(this.datos || {}).sort((a, b) => Number(b[1]) - Number(a[1]));
            const labels = entries.map(([key]) => this.resolveLabel(key));
            const data = entries.map(([, val]) => val);

            this.renderChart({
                labels,
                datasets: [
                    {
                        backgroundColor: this.bgBorder,
                        borderColor: this.bgColors,
                        borderWidth: 2,
                        data,
                    },
                ],
            }, this.options);
        },
    },
    watch: {
        datos: {
            deep: true,
            handler() {
                this.mostrar();
            },
        },
    },
    mounted() {
        this.mostrar();
    },
};
</script>

<style scoped>
#bar-chart {
    height: 260px !important;
    display: block !important;
}
</style>
