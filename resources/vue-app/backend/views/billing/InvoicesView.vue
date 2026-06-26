<template>
  <div>
    <page-top :show-add-btn="true" @open-modal="openModal">
      <div class="flex flex-col">
        <label class="text-xs font-medium text-gray-300">Status:</label>
        <select v-model="filterData.status" class="bg-gray-700 px-2 py-1 rounded hover:bg-gray-600 focus:outline-none">
          <option :value="undefined">All</option>
          <option value="draft">Draft</option>
          <option value="sent">Sent</option>
          <option value="partially_paid">Partially Paid</option>
          <option value="paid">Paid</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
    </page-top>

    <data-table :headers="tableHeaders">
      <tr v-for="(item, index) in dataList.data" :key="index"
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
        <td class="px-6 py-2">
          <router-link :to="`/admin/invoices/${item.id}`" class="font-medium text-blue-500 hover:underline">
            {{ item.invoice_number }}
          </router-link>
        </td>
        <td class="px-6 py-2 text-gray-400">
          {{ item.project ? item.project.customer.name : '—' }}
        </td>
        <td class="px-6 py-2 text-gray-400">
          {{ item.project ? item.project.project_name : '—' }}
        </td>
        <td class="px-6 py-2 text-gray-400">{{ item.invoice_date }}</td>
        <td class="px-6 py-2 text-right text-gray-300">{{ formatAmount(item.final_amount) }}</td>
        <td class="px-6 py-2 text-right text-green-400">{{ formatAmount(item.paid_amount) }}</td>
        <td class="px-6 py-2 text-right text-red-400">
          {{ formatAmount(item.final_amount - item.paid_amount) }}
        </td>
        <td class="px-6 py-2 text-center">
          <span :class="invoiceStatusClass(item.status)"
            class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
            {{ item.status.replace('_', ' ') }}
          </span>
        </td>
        <td class="px-6 py-2 text-right">
          <router-link :to="`/admin/invoices/${item.id}`"
            class="font-medium text-blue-500 hover:underline mr-2">View</router-link>
          <button @click="deleteItem(item.id, dataList.current_page)"
            class="font-medium text-red-500 hover:underline">Drop</button>
        </td>
      </tr>
    </data-table>

    <pagination :data="dataList" @fetch="fetchData" />

    <!-- Create Invoice Modal -->
    <modal :def-form-data="defFormData" extra-class="max-w-xl">
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Project <span class="text-red-500">*</span>
          </label>
          <select v-model="formData.project_id"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            <option :value="undefined">— Select Project —</option>
            <option v-for="p in projects" :key="p.id" :value="p.id">
              {{ p.project_name }} ({{ p.customer.name }})
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Invoice Date <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.invoice_date" type="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Due Date</label>
          <input v-model="formData.due_date" type="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Invoice Amount <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.invoice_amount" @input="calcFinal" type="number" min="0" placeholder="0.00"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Tax Amount</label>
          <input v-model="formData.tax_amount" @input="calcFinal" type="number" min="0" placeholder="0.00"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Discount Amount</label>
          <input v-model="formData.discount_amount" @input="calcFinal" type="number" min="0" placeholder="0.00"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <!-- Live Final Amount Preview -->
        <div class="col-span-2 bg-gray-100 dark:bg-gray-900 rounded-lg p-3 flex justify-between items-center">
          <span class="text-sm text-gray-500 dark:text-gray-400">Final Amount</span>
          <span class="text-lg font-bold text-white">{{ formatAmount(finalAmount) }}</span>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import PageTop from "../../components/PageTop";
import DataTable from "../../components/DataTable";
import Modal from "../../components/Modal";
import Pagination from "../../components/Pagination";

export default {
  name: "InvoicesView",
  components: { PageTop, DataTable, Modal, Pagination },

  data() {
    return {
      projects: [],
      defFormData: { invoice_amount: 0, tax_amount: 0, discount_amount: 0 },
      tableHeaders: [
        this.tableHeader({ name: 'invoice no' }),
        this.tableHeader({ name: 'customer' }),
        this.tableHeader({ name: 'project' }),
        this.tableHeader({ name: 'date' }),
        this.tableHeader({ name: 'final', cls: 'px-6 py-3 text-right' }),
        this.tableHeader({ name: 'paid', cls: 'px-6 py-3 text-right' }),
        this.tableHeader({ name: 'due', cls: 'px-6 py-3 text-right' }),
        this.tableHeader({ name: 'status', cls: 'px-6 py-3 text-center' }),
        this.tableHeader({ name: 'actions', cls: 'px-6 py-3 text-right' }),
      ],
    };
  },

  computed: {
    finalAmount() {
      const inv = parseFloat(this.formData.invoice_amount) || 0;
      const tax = parseFloat(this.formData.tax_amount) || 0;
      const dis = parseFloat(this.formData.discount_amount) || 0;
      return inv + tax - dis;
    },
  },

  mounted() {
    this.fetchData();
    this.loadProjects();
  },

  methods: {
    loadProjects() {
      this.httpReq({
        url: this.generateUrl('api/projects') + '?per_page=200',
        method: 'get',
        callback: (result) => {
          this.projects = result.data || [];
        },
      });
    },

    calcFinal() {
      // computed handles it — just triggers reactivity
    },

    invoiceStatusClass(status) {
      const map = {
        draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        sent: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        partially_paid: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        paid: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
      };
      return map[status] || 'bg-gray-100 text-gray-700';
    },

    formatAmount(val) {
      return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
    },
  },
};
</script>