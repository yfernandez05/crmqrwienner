<script>
import { Pie } from 'vue-chartjs';
import 'chartjs-plugin-labels';
import { bgColors, bgBorder } from './../../colorreport';

export default {
    extends: Pie,
    props: {
        datos: {
            type: Array,
            default: () => [],
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
                plugins: {
                    labels: {
                        render: 'percentage',
                        fontColor: 'black',
                        precision: 2,
                    },
                },
                legend: {
                    labels: {
                        usePointStyle: true,
                    },
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
            const labels = [];
            const data = [];

            (this.datos || []).forEach((item) => {
                labels.push(this.resolveLabel(item.opcion));
                data.push(item.total);
            });

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
#pie-chart {
    height: 260px !important;
    display: block !important;
}
</style>
