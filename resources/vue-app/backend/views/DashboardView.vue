<template>
    <div>
        <!-- Page Title -->
        <div class="mb-6 p-3 py-2 bg-gradient-to-r from-red-200 to-gray-400 dark:from-red-900 dark:to-gray-400 rounded">
            <h2 class="text-3xl font-semibold">Dashboard</h2>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customers</p>
                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                        <i class="fa-solid fa-users text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    {{ loading ? '...' : stats.total_customers }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Active customers</p>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Projects</p>
                    <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                        <i class="fa-solid fa-diagram-project text-purple-600 dark:text-purple-400 text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    {{ loading ? '...' : stats.total_projects }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Total projects</p>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Invoice Amount</p>
                    <div class="w-9 h-9 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice-dollar text-yellow-600 dark:text-yellow-400 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-gray-900 dark:text-white">
                    {{ loading ? '...' : formatAmount(stats.total_invoice_amount) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Total invoiced</p>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Received</p>
                    <div class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-green-500">
                    {{ loading ? '...' : formatAmount(stats.total_received) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Total collected</p>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Due</p>
                    <div class="w-9 h-9 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 dark:text-red-400 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-red-500">
                    {{ loading ? '...' : formatAmount(stats.total_due) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Outstanding due</p>
            </div>

        </div>

        <!-- Recent Invoices -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    <i class="fa-solid fa-clock-rotate-left text-gray-400 mr-2"></i>
                    Recent Invoices
                </h3>
                <router-link to="/admin/invoices" class="text-sm text-blue-500 hover:underline">View all</router-link>
            </div>

            <div v-if="loading" class="text-center py-10 text-gray-400">
                <i class="fa-solid fa-spinner fa-spin text-2xl"></i>
            </div>

            <div v-else-if="recentInvoices.length === 0" class="text-center py-10 text-gray-400 text-sm">
                No invoices yet.
            </div>

            <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Invoice No</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Final</th>
                        <th class="px-6 py-3 text-right">Paid</th>
                        <th class="px-6 py-3 text-right">Due</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(inv, i) in recentInvoices" :key="i"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-3">
                            <router-link :to="`/admin/invoices/${inv.id}`"
                                class="font-medium text-blue-500 hover:underline">
                                {{ inv.invoice_number }}
                            </router-link>
                        </td>
                        <td class="px-6 py-3">{{ inv.project.customer.name }}</td>
                        <td class="px-6 py-3">{{ inv.project.project_name }}</td>
                        <td class="px-6 py-3">{{ inv.invoice_date }}</td>
                        <td class="px-6 py-3 text-right text-gray-300">{{ formatAmount(inv.final_amount) }}</td>
                        <td class="px-6 py-3 text-right text-green-400">{{ formatAmount(inv.paid_amount) }}</td>
                        <td class="px-6 py-3 text-right text-red-400">
                            {{ formatAmount(inv.final_amount - inv.paid_amount) }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span :class="statusClass(inv.status)"
                                class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
                                {{ inv.status.replace('_', ' ') }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
export default {
    name: "DashboardView",

    data() {
        return {
            loading: true,
            stats: {
                total_customers: 0,
                total_projects: 0,
                total_invoice_amount: 0,
                total_received: 0,
                total_due: 0,
            },
            recentInvoices: [],
        };
    },

    mounted() {
        this.loadDashboard();
        this.loadRecentInvoices();
    },

    methods: {
        loadDashboard() {
            this.httpReq({
                url: this.generateUrl('api/dashboard'),
                method: 'get',
                callback: (result) => {
                    this.stats = result;
                    this.loading = false;
                },
            });
        },

        loadRecentInvoices() {
            this.httpReq({
                url: this.generateUrl('api/invoices') + '?per_page=5',
                method: 'get',
                callback: (result) => {
                    this.recentInvoices = result.data || [];
                },
            });
        },

        statusClass(status) {
            const map = {
                draft: 'bg-gray-100 text-gray-700',
                sent: 'bg-blue-100 text-blue-700',
                partially_paid: 'bg-yellow-100 text-yellow-700',
                paid: 'bg-green-100 text-green-700',
                cancelled: 'bg-red-100 text-red-700',
            };
            return map[status] || 'bg-gray-100 text-gray-700';
        },
    },
};
</script>