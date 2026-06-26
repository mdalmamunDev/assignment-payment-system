<template>
  <div>
    <div
      class="flex items-center gap-3 mb-6 p-3 py-2 bg-gradient-to-r from-red-200 to-gray-400 dark:from-red-900 dark:to-gray-400 rounded">
      <back-button />
      <h2 class="text-2xl font-semibold">Add Payment</h2>
    </div>

    <div v-if="!invoice" class="text-center text-gray-400 py-20">
      <i class="fa-solid fa-spinner fa-spin text-3xl"></i>
    </div>

    <div v-else class="max-w-xl mx-auto">
      <!-- Invoice Summary -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
        <div class="flex justify-between text-sm text-gray-400 mb-1">
          <span>Invoice</span>
          <span class="font-medium text-white">{{ invoice.invoice_number }}</span>
        </div>
        <div class="flex justify-between text-sm text-gray-400 mb-1">
          <span>Customer</span>
          <span>{{ invoice.project.customer.name }}</span>
        </div>
        <div class="flex justify-between text-sm text-gray-400 mb-1">
          <span>Final Amount</span>
          <span>{{ formatAmount(invoice.final_amount) }}</span>
        </div>
        <div class="flex justify-between text-sm text-gray-400 mb-1">
          <span>Paid</span>
          <span class="text-green-400">{{ formatAmount(invoice.paid_amount) }}</span>
        </div>
        <div class="flex justify-between font-bold text-red-400 border-t dark:border-gray-600 pt-2 mt-2">
          <span>Due Amount</span>
          <span>{{ formatAmount(invoice.final_amount - invoice.paid_amount) }}</span>
        </div>
      </div>

      <!-- Payment Form -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
              Amount <span class="text-red-500">*</span>
            </label>
            <input v-model="form.amount" type="number" min="0.01" :max="invoice.final_amount - invoice.paid_amount"
              placeholder="0.00"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
              Payment Date <span class="text-red-500">*</span>
            </label>
            <input v-model="form.payment_date" type="date"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
              Payment Method <span class="text-red-500">*</span>
            </label>
            <select v-model="form.payment_method"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
              <option value="cash">Cash</option>
              <option value="bank">Bank Transfer</option>
              <option value="mobile_banking">Mobile Banking</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Reference No</label>
            <input v-model="form.reference_no" type="text" placeholder="Optional"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Note</label>
            <input v-model="form.note" type="text" placeholder="Optional"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" />
          </div>
        </div>

        <button @click="submitPayment"
          class="mt-6 w-full text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5">
          <i class="fa-solid fa-check mr-2"></i> Save Payment
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import BackButton from "../../components/BackButton";

export default {
  name: "PaymentView",
  components: { BackButton },

  data() {
    return {
      invoice: null,
      form: {
        invoice_id: null,
        amount: '',
        payment_date: new Date().toISOString().split('T')[0],
        payment_method: 'cash',
        reference_no: '',
        note: '',
      },
    };
  },

  mounted() {
    this.loadInvoice();
  },

  methods: {
    loadInvoice() {
      this.httpReq({
        url: this.generateUrl(`api/invoices/${this.$route.params.id}`),
        method: 'get',
        callback: (result) => {
          this.invoice = result;
          this.form.invoice_id = result.id;
        },
      });
    },

    submitPayment() {
      this.httpReq({
        url: this.generateUrl('api/payments'),
        method: 'post',
        data: this.form,
        callback: () => {
          this.$router.push(`/admin/invoices/${this.invoice.id}`);
        },
      });
    },

    formatAmount(val) {
      return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
    },
  },
};
</script>