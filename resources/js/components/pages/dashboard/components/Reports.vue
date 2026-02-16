<template>
    <DashboardLayout>
        <div class="reports-container">
            <div class="reports-header">
                <h1 class="title">Звіти</h1>
                <div class="year-selector" v-if="revenueData">
                    <label>Рік:</label>
                    <el-select v-model="selectedYear" @change="fetchRevenueData" class="year-select">
                        <el-option
                            v-for="y in yearOptions"
                            :key="y"
                            :label="y"
                            :value="y"
                        />
                    </el-select>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h2>Виручка за місяцями</h2>
                        <el-button type="primary" size="small" :loading="exportingRevenue" @click="downloadRevenueExcel">
                            <el-icon><Download /></el-icon>
                            Excel
                        </el-button>
                    </div>
                    <div class="chart-body">
                        <Bar v-if="revenueData" :data="revenueChartData" :options="revenueChartOptions" />
                        <div v-else class="chart-loading">
                            <el-icon class="is-loading"><Loading /></el-icon>
                            <span>Завантаження...</span>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h2>Популярні послуги</h2>
                        <el-button type="primary" size="small" :loading="exportingPopular" @click="downloadPopularExcel">
                            <el-icon><Download /></el-icon>
                            Excel
                        </el-button>
                    </div>
                    <div class="chart-body">
                        <Bar v-if="popularData" :data="popularChartData" :options="popularChartOptions" />
                        <div v-else class="chart-loading">
                            <el-icon class="is-loading"><Loading /></el-icon>
                            <span>Завантаження...</span>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h2>Записи за статусами</h2>
                        <el-button type="primary" size="small" :loading="exportingStatus" @click="downloadStatusExcel">
                            <el-icon><Download /></el-icon>
                            Excel
                        </el-button>
                    </div>
                    <div class="chart-body">
                        <Doughnut v-if="statusData" :data="statusChartData" :options="statusChartOptions" />
                        <div v-else class="chart-loading">
                            <el-icon class="is-loading"><Loading /></el-icon>
                            <span>Завантаження...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
} from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs';
import DashboardLayout from '../../../../layouts/DashboardLayout.vue';
import { Download, Loading } from '@element-plus/icons-vue';
import axios from 'axios';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

const CHART_COLORS = [
    '#f97316', '#22c55e', '#3b82f6', '#a855f7', '#ec4899',
    '#eab308', '#06b6d4', '#84cc16'
];

export default {
    name: 'Reports',
    components: {
        DashboardLayout,
        Bar,
        Doughnut,
        Download,
        Loading
    },
    data() {
        const currentYear = new Date().getFullYear();
        return {
            selectedYear: currentYear,
            yearOptions: Array.from({ length: 5 }, (_, i) => currentYear - i),
            revenueData: null,
            popularData: null,
            statusData: null,
            exportingRevenue: false,
            exportingPopular: false,
            exportingStatus: false
        };
    },
    computed: {
        revenueChartData() {
            if (!this.revenueData) return null;
            return {
                labels: this.revenueData.labels,
                datasets: [{
                    label: 'Виручка (грн)',
                    data: this.revenueData.values,
                    backgroundColor: 'rgba(249, 115, 22, 0.7)',
                    borderColor: '#f97316',
                    borderWidth: 1
                }]
            };
        },
        revenueChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${Number(ctx.raw).toLocaleString('uk-UA')} грн`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        ticks: { color: '#94a3b8' }
                    },
                    x: {
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        ticks: { color: '#94a3b8', maxRotation: 45 }
                    }
                }
            };
        },
        popularChartData() {
            if (!this.popularData) return null;
            return {
                labels: this.popularData.labels,
                datasets: [{
                    label: 'Кількість',
                    data: this.popularData.values,
                    backgroundColor: CHART_COLORS.slice(0, this.popularData.labels.length),
                    borderWidth: 1
                }]
            };
        },
        popularChartOptions() {
            return {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw} замовлень`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    }
                }
            };
        },
        statusChartData() {
            if (!this.statusData) return null;
            return {
                labels: this.statusData.labels,
                datasets: [{
                    data: this.statusData.values,
                    backgroundColor: CHART_COLORS.slice(0, this.statusData.labels.length),
                    borderWidth: 2,
                    borderColor: '#1e293b'
                }]
            };
        },
        statusChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', padding: 16 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                return `${ctx.label}: ${ctx.raw} (${pct}%)`;
                            }
                        }
                    }
                }
            };
        }
    },
    mounted() {
        if (!this.$store.state.isAdmin) {
            this.$router.push({ name: 'Dashboard' });
            return;
        }
        this.fetchAllData();
    },
    methods: {
        async fetchAllData() {
            await Promise.all([
                this.fetchRevenueData(),
                this.fetchPopularData(),
                this.fetchStatusData()
            ]);
        },
        async fetchRevenueData() {
            try {
                const res = await axios.get('/api/reports/revenue-by-month', {
                    params: { year: this.selectedYear }
                });
                this.revenueData = res.data;
            } catch (e) {
                this.$message?.error?.('Помилка завантаження виручки');
            }
        },
        async fetchPopularData() {
            try {
                const res = await axios.get('/api/reports/popular-services');
                this.popularData = res.data;
            } catch (e) {
                this.$message?.error?.('Помилка завантаження популярних послуг');
            }
        },
        async fetchStatusData() {
            try {
                const res = await axios.get('/api/reports/bookings-by-status');
                this.statusData = res.data;
            } catch (e) {
                this.$message?.error?.('Помилка завантаження статусів');
            }
        },
        async downloadRevenueExcel() {
            this.exportingRevenue = true;
            try {
                const res = await axios.get('/api/reports/export/revenue-by-month', {
                    params: { year: this.selectedYear },
                    responseType: 'blob'
                });
                this.downloadBlob(res.data, `vuruchka-${this.selectedYear}.xlsx`);
                this.$message?.success?.('Файл завантажено');
            } catch (e) {
                this.$message?.error?.('Помилка експорту');
            } finally {
                this.exportingRevenue = false;
            }
        },
        async downloadPopularExcel() {
            this.exportingPopular = true;
            try {
                const res = await axios.get('/api/reports/export/popular-services', {
                    responseType: 'blob'
                });
                this.downloadBlob(res.data, 'populyarni-posluhy.xlsx');
                this.$message?.success?.('Файл завантажено');
            } catch (e) {
                this.$message?.error?.('Помилка експорту');
            } finally {
                this.exportingPopular = false;
            }
        },
        async downloadStatusExcel() {
            this.exportingStatus = true;
            try {
                const res = await axios.get('/api/reports/export/bookings-by-status', {
                    responseType: 'blob'
                });
                this.downloadBlob(res.data, 'zapysy-za-statusamy.xlsx');
                this.$message?.success?.('Файл завантажено');
            } catch (e) {
                this.$message?.error?.('Помилка експорту');
            } finally {
                this.exportingStatus = false;
            }
        },
        downloadBlob(blob, filename) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    }
};
</script>
