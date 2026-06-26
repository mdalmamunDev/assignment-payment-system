<template>
  <div>
    <page-top :show-add-btn="false">
      <div class="flex flex-col">
        <label class="text-xs font-medium text-gray-300">Status:</label>
        <select v-model="filterData.status" class="bg-gray-700 px-2 py-1 rounded hover:bg-gray-600 focus:outline-none">
          <option :value="undefined">All</option>
          <option value="sent">Sent</option>
          <option value="partially_paid">Partially Paid</option>
          <option value="paid">Paid</option>
        </select>
      </div>
      <div class="flex flex-col">
        <label class="text-xs font-medium text-gray-300">From:</label>
        <input v-model="filterData.date_from" type="date"
          class="bg-gray-700 px-2 py-1 rounded hover:bg-gray-600 focus:outline-none text-white text-xs" />
      </div>
      <div class="flex flex-col">
        <label class="text-xs font-medium text-gray-300">To:</label>
        <input v-model="filterData.date_to" type="date"
          class="bg-gray-700 px-2 py-1 rounded hover:bg-gray-600 focus:outline-none text-white text-xs" />
      </div>
    </page-top>

    <!-- Summary Totals -->
    <div class="grid grid-cols-3 gap-4 mb-4" v-if="dataList.data">
      <div class="bg-gray-800 rounded-lg p-4 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-wider">Total Final</p>
        <p class="text-xl font-bold text-white mt-1">{{ formatAmount(totals.final) }}</p>
      </div>
      <div class="bg-gray-800 rounded-lg p-4 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-wider">Total Paid</p>
        <p class="text-xl font-bold text-green-400 mt-1">{{ formatAmount(totals.paid) }}</p>
      </div>
      <div class="bg-gray-800 rounded-lg p-4 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-wider">Total Due</p>
        <p class="text-xl font-bold text-red-400 mt-1">{{ formatAmount(totals.due) }}</p>
      </div>
    </div>

    <data-table :headers="tableHeaders">
      <tr v-for="(item, index) in dataList.data" :key="index"
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
        <td class="px-6 py-2">
          <router-link :to="`/admin/invoices/${item.id}`" class="font-medium text-blue-500 hover:underline">
            {{ item.invoice_number }}
          </router-link>
        </td>
        <td class="px-6 py-2 text-gray-400">{{ item.project.customer.name }}</td>
        <td class="px-6 py-2 text-gray-400">{{ item.project.project_name }}</td>
        <td class="px-6 py-2 text-gray-400">{{ item.invoice_date }}</td>
        <td class="px-6 py-2 text-right text-gray-300">{{ formatAmount(item.final_amount) }}</td>
        <td class="px-6 py-2 text-right text-green-400">{{ formatAmount(item.paid_amount) }}</td>
        <td class="px-6 py-2 text-right text-red-400 font-medium">
          {{ formatAmount(item.final_amount - item.paid_amount) }}
        </td>
        <td class="px-6 py-2 text-center">
          <span :class="invoiceStatusClass(item.status)"
            class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
            {{ item.status.replace('_', ' ') }}
          </span>
        </td>
      </tr>
    </data-table>

    <pagination :data="dataList" @fetch="fetchData" />
  </div>
</template>

<script>
import PageTop from "../../components/PageTop";
import DataTable from "../../components/DataTable";
import Pagination from "../../components/Pagination";

export default {
  name: "DueReportView",
  components: { PageTop, DataTable, Pagination },

  data() {
    return {
      tableHeaders: [
        this.tableHeader({ name: 'invoice no' }),
        this.tableHeader({ name: 'customer' }),
        this.tableHeader({ name: 'project' }),
        this.tableHeader({ name: 'date' }),
        this.tableHeader({ name: 'final', cls: 'px-6 py-3 text-right' }),
        this.tableHeader({ name: 'paid', cls: 'px-6 py-3 text-right' }),
        this.tableHeader({ name: 'due', cls: 'px-6 py-3 text-right' }),
        this.tableHeader({ name: 'status', cls: 'px-6 py-3 text-center' }),
      ],
    };
  },

  computed: {
    totals() {
      const data = this.dataList.data || [];
      return {
        final: data.reduce((s, i) => s + parseFloat(i.final_amount), 0),
        paid: data.reduce((s, i) => s + parseFloat(i.paid_amount), 0),
        due: data.reduce((s, i) => s + (parseFloat(i.final_amount) - parseFloat(i.paid_amount)), 0),
      };
    },
  },

  mounted() {
    this.fetchData();
  },

  methods: {
    invoiceStatusClass(status) {
      const map = {
        sent: 'bg-blue-100 text-blue-700',
        partially_paid: 'bg-yellow-100 text-yellow-700',
        paid: 'bg-green-100 text-green-700',
      };
      return map[status] || 'bg-gray-100 text-gray-700';
    },

    formatAmount(val) {
      return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
    },
  },
};
</script>