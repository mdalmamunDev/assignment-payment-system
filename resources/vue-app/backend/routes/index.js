import DashboardView from "../views/DashboardView";
import CustomersView from "../views/billing/CustomersView";
import ProjectsView from "../views/billing/ProjectsView";
import InvoicesView from "../views/billing/InvoicesView";
import InvoiceDetailView from "../views/billing/InvoiceDetailView";
import PaymentView from "../views/billing/PaymentView";
import DueReportView from "../views/billing/DueReportView";


const route = [
    {
        path : '/admin/dashboard',
        name : 'dashboard',
        component : DashboardView,
        meta : {'pageTitle' : 'Dashboard'},
    },
    {
        path: '/admin/customers',
        name: 'customers',
        component: CustomersView,
        meta: { pageTitle: 'Customers', dataUrl: 'api/customers' },
    },
    {
        path: '/admin/projects',
        name: 'projects',
        component: ProjectsView,
        meta: { pageTitle: 'Projects', dataUrl: 'api/projects' },
    },
    {
        path: '/admin/invoices',
        name: 'invoices',
        component: InvoicesView,
        meta: { pageTitle: 'Invoices', dataUrl: 'api/invoices' },
    },
    {
        path: '/admin/invoices/:id',
        name: 'invoice-detail',
        component: InvoiceDetailView,
        meta: { pageTitle: 'Invoice Detail' },
    },
    {
        path: '/admin/invoices/:id/payment',
        name: 'invoice-payment',
        component: PaymentView,
        meta: { pageTitle: 'Add Payment' },
    },
    {
        path: '/admin/reports/due',
        name: 'due-report',
        component: DueReportView,
        meta: { pageTitle: 'Due Report', dataUrl: 'api/reports/due' },
    },


    // {
    //     path : '/admin/items/form/:id?',
    //     name : 'items',
    //     component : ItemsFormView,
    //     meta : {'pageTitle' : 'Item Form'},
    // },
    // {
    //     path : '/admin/industries',
    //     component : IndustriesView,
    //     meta : {'pageTitle' : 'Industries', dataUrl: 'api/industries'},
    // },
    // {
    //     path : '/admin/genres',
    //     component : GenresView,
    //     meta : {'pageTitle' : 'Genres', dataUrl: 'api/genres'},
    // },
    // {
    //     path : '/admin/tags',
    //     component : TagsView,
    //     meta : {'pageTitle' : 'Tags', dataUrl: 'api/tags'},
    // },
    // {
    //     path: '/admin/dashboard',
    //     name: 'dashboard',
    //     component: DashboardView,
    //     meta: { pageTitle: 'Dashboard' },
    // },
];
export default route;