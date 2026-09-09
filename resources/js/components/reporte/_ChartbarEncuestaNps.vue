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
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Puntaje NPS',
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
        mostrar() {
            const keys = Object.keys(this.datos || {}).sort((a, b) => Number(a) - Number(b));
            const labels = keys;
            const data = keys.map((k) => this.datos[k]);

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
