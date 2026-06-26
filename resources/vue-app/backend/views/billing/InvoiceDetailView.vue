<template>
  <div>
    <!-- Top Bar -->
    <div
      class="flex items-center justify-between mb-6 p-3 py-2 bg-gradient-to-r from-red-200 to-gray-400 dark:from-red-900 dark:to-gray-400 rounded">
      <div class="flex items-center gap-3">
        <back-button />
        <h2 class="text-2xl font-semibold">Invoice Detail</h2>
      </div>
      <div class="flex gap-2" v-if="invoice">
        <button v-if="invoice.status !== 'cancelled' && invoice.status !== 'paid'" @click="goToPayment"
          class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-4 py-2">
          <i class="fa-solid fa-plus mr-1"></i> Add Payment
        </button>
        <button v-if="invoice.status !== 'cancelled'" @click="cancelInvoice"
          class="text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-4 py-2">
          Cancel Invoice
        </button>
        <a :href="`/admin/invoices/${invoice.id}/print`" target="_blank"
          class="text-white bg-gray-600 hover:bg-gray-700 font-medium rounded-lg text-sm px-4 py-2">
          <i class="fa-solid fa-print mr-1"></i> Print
        </a>
      </div>
    </div>

    <div v-if="!invoice" class="text-center text-gray-400 py-20">
      <i class="fa-solid fa-spinner fa-spin text-3xl"></i>
    </div>

    <div v-else class="grid grid-cols-3 gap-4">

      <!-- Invoice Info -->
      <div class="col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ invoice.invoice_number }}</h3>
            <p class="text-sm text-gray-400 mt-1">Date: {{ invoice.invoice_date }}</p>
            <p v-if="invoice.due_date" class="text-sm text-gray-400">Due: {{ invoice.due_date }}</p>
          </div>
          <span :class="invoiceStatusClass(invoice.status)"
            class="px-3 py-1 rounded-full text-sm font-semibold capitalize">
            {{ invoice.status.replace('_', ' ') }}
          </span>
        </div>

        <!-- Customer & Project -->
        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Customer</p>
            <p class="font-semibold text-gray-900 dark:text-white">
              {{ invoice.project.customer.name }}
            </p>
            <p class="text-sm text-gray-400">{{ invoice.project.customer.email }}</p>
            <p class="text-sm text-gray-400">{{ invoice.project.customer.phone }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Project</p>
            <p class="font-semibold text-gray-900 dark:text-white">{{ invoice.project.project_name }}</p>
            <p class="text-sm text-gray-400">{{ invoice.project.project_code }}</p>
          </div>
        </div>

        <!-- Amount Breakdown -->
        <div class="border-t dark:border-gray-600 pt-4 space-y-2">
          <div class="flex justify-between text-sm text-gray-400">
            <span>Invoice Amount</span>
            <span>{{ formatAmount(invoice.invoice_amount) }}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-400">
            <span>Tax Amount</span>
            <span>+ {{ formatAmount(invoice.tax_amount) }}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-400">
            <span>Discount Amount</span>
            <span>- {{ formatAmount(invoice.discount_amount) }}</span>
          </div>
          <div
            class="flex justify-between font-bold text-gray-900 dark:text-white border-t dark:border-gray-600 pt-2 mt-2">
            <span>Final Amount</span>
            <span>{{ formatAmount(invoice.final_amount) }}</span>
          </div>
          <div class="flex justify-between text-green-500 font-medium">
            <span>Paid Amount</span>
            <span>{{ formatAmount(invoice.paid_amount) }}</span>
          </div>
          <div class="flex justify-between text-red-400 font-bold text-lg">
            <span>Due Amount</span>
            <span>{{ formatAmount(invoice.final_amount - invoice.paid_amount) }}</span>
          </div>
        </div>
      </div>

      <!-- Payment History -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
          <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
          Payment History
        </h4>

        <div v-if="invoice.payments.length === 0" class="text-center text-gray-400 py-8 text-sm">
          No payments yet.
        </div>

        <div v-for="(payment, i) in invoice.payments" :key="i"
          class="border-b dark:border-gray-700 pb-3 mb-3 last:border-0 last:mb-0">
          <div class="flex justify-between items-start">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ formatAmount(payment.amount) }}
              </p>
              <p class="text-xs text-gray-400">{{ payment.payment_date }}</p>
              <p class="text-xs text-gray-400 capitalize">
                {{ payment.payment_method.replace('_', ' ') }}
              </p>
              <p v-if="payment.reference_no" class="text-xs text-gray-400">
                Ref: {{ payment.reference_no }}
              </p>
            </div>
            <button @click="deletePayment(payment.id)" class="text-red-500 hover:text-red-400 text-xs">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import BackButton from "../../components/BackButton";

export default {
  name: "InvoiceDetailView",
  components: { BackButton },

  data() {
    return { invoice: null };
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
        },
      });
    },

    goToPayment() {
      this.$router.push(`/admin/invoices/${this.invoice.id}/payment`);
    },

    cancelInvoice() {
      this.$swal({
        title: 'Cancel Invoice?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
      }).then((res) => {
        if (!res.isConfirmed) return;
        this.httpReq({
          url: this.generateUrl(`api/invoices/${this.invoice.id}/cancel`),
          method: 'patch',
          callback: () => this.loadInvoice(),
        });
      });
    },

    deletePayment(id) {
      this.$swal({
        title: 'Delete Payment?',
        text: 'Invoice status will be recalculated.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete!',
      }).then((res) => {
        if (!res.isConfirmed) return;
        this.httpReq({
          url: this.generateUrl(`api/payments/${id}`),
          method: 'delete',
          callback: () => this.loadInvoice(),
        });
      });
    },

    invoiceStatusClass(status) {
      const map = {
        draft: 'bg-gray-100 text-gray-700',
        sent: 'bg-blue-100 text-blue-700',
        partially_paid: 'bg-yellow-100 text-yellow-700',
        paid: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
      };
      return map[status] || 'bg-gray-100 text-gray-700';
    },

    formatAmount(val) {
      return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
    },
  },
};
</script>