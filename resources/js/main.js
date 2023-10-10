import {createApp} from 'vue';
import DataTable from "./components/DataTable.vue";
import Modal from "./components/Modal.vue";

const app = createApp({});

app.component('generic-data-table', DataTable);
app.component('generic-modal', Modal);

export default app;
