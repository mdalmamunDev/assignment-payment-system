<template>
  <div>
    <page-top :show-add-btn="true" @open-modal="openModal">
      <div class="flex flex-col">
        <label class="text-xs font-medium text-gray-300">Status:</label>
        <select v-model="filterData.status" class="bg-gray-700 px-2 py-1 rounded hover:bg-gray-600 focus:outline-none">
          <option :value="undefined">All</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </page-top>

    <data-table :headers="tableHeaders">
      <tr v-for="(item, index) in dataList.data" :key="index"
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
        <td class="px-6 py-2 font-medium text-gray-900 dark:text-white">{{ item.name }}</td>
        <td class="px-6 py-2 text-gray-400">{{ item.email }}</td>
        <td class="px-6 py-2 text-gray-400">{{ item.phone || '—' }}</td>
        <td class="px-6 py-2 text-gray-400">{{ item.company_name || '—' }}</td>
        <td class="px-6 py-2 text-center">
          <span :class="statusClass(item.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
            {{ item.status === 'active' ? 'Active' : 'Inactive' }}
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
    <modal :def-form-data="defFormData" extra-class="max-w-lg">
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Name <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.name" type="text" placeholder="Full name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
            Email <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.email" type="email" placeholder="email@example.com"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Phone</label>
          <input v-model="formData.phone" type="text" placeholder="Phone number"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Company Name</label>
          <input v-model="formData.company_name" type="text" placeholder="Company"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Status</label>
          <select v-model="formData.status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Address</label>
          <textarea v-model="formData.address" rows="2" placeholder="Address"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"></textarea>
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
  name: "CustomersView",
  components: { PageTop, DataTable, Modal, Pagination },

  data() {
    return {
      defFormData: { status: 'active' },
      tableHeaders: [
        this.tableHeader({ name: 'name' }),
        this.tableHeader({ name: 'email' }),
        this.tableHeader({ name: 'phone' }),
        this.tableHeader({ name: 'company' }),
        this.tableHeader({ name: 'status', cls: 'px-6 py-3 text-center' }),
        this.tableHeader({ name: 'actions', cls: 'px-6 py-3 text-right' }),
      ],
    };
  },

  mounted() {
    this.fetchData();
  },

  methods: {
    statusClass(status) {
      return status === 'active'
        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
        : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
    },
  },
};
</script>