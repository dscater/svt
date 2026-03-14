<script setup>
import Content from "@/Components/Content.vue";
import { computed, onMounted, ref } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import { useAppStore } from "@/stores/aplicacion/appStore";
const appStore = useAppStore();

const cargarListas = () => {};

const listSucursals = ref([]);

onMounted(() => {
    appStore.stopLoading();
    cargarListas();
    setTimeout(() => {}, 300);
});

const form = ref({
    tipoR: "pdf",
    unidad: "todos",
    fecha_ini: "",
    fecha_fin: "",
});

const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listUnidad = ref([
    {
        value: "pdf",
        label: "PDF",
    },
    {
        value: "excel",
        label: "EXCEL",
    },
]);

const listTipos = ref([
    { value: "todos", label: "TODOS" },
    { value: "ANAPOL", label: "ANAPOL" },
    { value: "FATESCIPOL", label: "FATESCIPOL" },
    { value: "ESBAPOLMUS", label: "ESBAPOLMUS" },
]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_postulantes", form.value);
    console.log(url);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};
</script>
<template>
    <Head title="Reporte Lista de Preinscritos"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lista de Preinscritos</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Reportes > Lista de Preinscritos
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <!-- END page-header -->
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="generarReporte">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="required"
                                        >Tipo de reporte</label
                                    >
                                    <select
                                        v-model="form.tipoR"
                                        class="form-control"
                                    >
                                        <option
                                            v-for="item in listUnidad"
                                            :value="item.value"
                                        >
                                            {{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="required"
                                        >Seleccionar unidad</label
                                    >
                                    <select
                                        :hide-details="
                                            form.errors?.unidad ? false : true
                                        "
                                        :error="
                                            form.errors?.unidad ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.unidad
                                                ? form.errors?.unidad
                                                : ''
                                        "
                                        v-model="form.unidad"
                                        class="form-control"
                                    >
                                        <option
                                            v-for="item in listTipos"
                                            :value="item.value"
                                        >
                                            {{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label>Fecha de registro</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="form.fecha_ini"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="form.fecha_fin"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center mt-3">
                                    <button
                                        class="btn btn-success"
                                        block
                                        @click="generarReporte"
                                        :disabled="generando"
                                        v-text="txtBtn"
                                    ></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
