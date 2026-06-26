<template>
  <div>
    <page-top :show-add-btn="true" @open-modal="openModal">
      <div class="flex flex-col">
        <label class="text-xs font-medium text-gray-300">Status:</label>
        <select v-model="filterData.status" class="bg-gray-700 px-2 py-1 rounded hover:bg-gray-600 focus:outline-none">
          <option :value="undefined">All</option>
          <option value="pending">Pending</option>
          <option value="running">Running</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
    </page-top>

    <data-table :headers="tableHeaders">
      <tr v-for="(item, index) in dataList.data" :key="index"
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
        <td class="px-6 py-2">
          <div class="font-medium text-gray-900 dark:text-white">{{ item.project_name }}</div>
          <div class="text-xs text-gray-400">{{ item.project_code }}</div>
        </td>
        <td class="px-6 py-2 text-gray-400">
          {{ item.customer ? item.customer.name : '—' }}
        </td>
        <td class="px-6 py-2 text-gray-400">{{ item.start_date }}</td>
        <td class="px-6 py-2 text-gray-400">{{ item.deadline || '—' }}</td>
        <td class="px-6 py-2 text-gray-400">{{ formatAmount(item.budget_amount) }}</td>
        <td class="px-6 py-2 text-center">
          <span :class="projectStatusClass(item.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
            {{ item.status }}
          </span>
        </td>
        <td class="px-6 py-2 text-right">
          <button @click="onClickUpdate(item)" class="font-medium text-yellow-500 hover:underline mr-2">Edit</button>
          <button @click="deleteItem(item.id, dataList.current_page)"
            class="font-medium text-red-500 hover:underline">Drop</button>
        </td>
      </tr>
    </data-table>

    <pagination :data="dataList" @fetch="fetchData" />

    <!-- Modal -->
    <modal :def-form-data="defFormData" extra-class="max-w-xl">
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Customer <span class="text-red-500">*</span>
          </label>
          <select v-model="formData.customer_id"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            <option :value="undefined">— Select Customer —</option>
            <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Project Name <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.project_name" type="text" placeholder="Project name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Start Date <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.start_date" type="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Deadline</label>
          <input v-model="formData.deadline" type="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Budget Amount <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.budget_amount" type="number" min="0" placeholder="0.00"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Status</label>
          <select v-model="formData.status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            <option value="pending">Pending</option>
            <option value="running">Running</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
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
  name: "ProjectsView",
  components: { PageTop, DataTable, Modal, Pagination },

  data() {
    return {
      customers: [],
      defFormData: { status: 'pending' },
      tableHeaders: [
        this.tableHeader({ name: 'project' }),
        this.tableHeader({ name: 'customer' }),
        this.tableHeader({ name: 'start date' }),
        this.tableHeader({ name: 'deadline' }),
        this.tableHeader({ name: 'budget' }),
        this.tableHeader({ name: 'status', cls: 'px-6 py-3 text-center' }),
        this.tableHeader({ name: 'actions', cls: 'px-6 py-3 text-right' }),
      ],
    };
  },

  mounted() {
    this.fetchData();
    this.loadCustomers();
  },

  methods: {
    loadCustomers() {
      this.httpReq({
        url: this.generateUrl('api/customers') + '?per_page=200',
        method: 'get',
        callback: (result) => {
          this.customers = result.data || [];
        },
      });
    },

    projectStatusClass(status) {
      const map = {
        pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        running: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        completed: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
      };
      return map[status] || 'bg-gray-100 text-gray-700';
    },

    formatAmount(val) {
      return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2 });
    },
  },
};
</script>