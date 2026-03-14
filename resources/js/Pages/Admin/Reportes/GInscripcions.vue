<script setup>
import Content from "@/Components/Content.vue";
import { computed, nextTick, onMounted, ref } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import Highcharts from "highcharts";
import exporting from "highcharts/modules/exporting";
import accessibility from "highcharts/modules/accessibility";
import { useAppStore } from "@/stores/aplicacion/appStore";
const appStore = useAppStore();
exporting(Highcharts);
accessibility(Highcharts);
Highcharts.setOptions({
    lang: {
        downloadPNG: "Descargar PNG",
        downloadJPEG: "Descargar JPEG",
        downloadPDF: "Descargar PDF",
        downloadSVG: "Descargar SVG",
        printChart: "Imprimir gráfico",
        contextButtonTitle: "Menú de exportación",
        viewFullscreen: "Pantalla completa",
        exitFullscreen: "Salir de pantalla completa",
    },
});
const cargarListas = () => {};

const listSucursals = ref([]);

onMounted(() => {
    appStore.stopLoading();
    cargarListas();
    setTimeout(() => {}, 300);
});

const form = ref({
    tipoR: "todos",
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
    return "Generar Gráfico";
});

const listTipoInscripcion = ref([
    {
        value: "todos",
        label: "TODOS",
    },
    {
        value: "PREINSCRITO",
        label: "PREINSCRIPCIÓN",
    },
    {
        value: "INSCRITO",
        label: "INSCRITOS",
    },
]);

const listUnidads = ref([
    { value: "todos", label: "TODOS" },
    { value: "ANAPOL", label: "ANAPOL" },
    { value: "FATESCIPOL", label: "FATESCIPOL" },
    { value: "ESBAPOLMUS", label: "ESBAPOLMUS" },
]);

const generarReporte = () => {
    generando.value = true;
    axios
        .get(route("reportes.r_ginscripcions"), {
            params: form.value,
        })
        .then((response) => {
            nextTick(() => {
                const containerId = `container`;
                const container = document.getElementById(containerId);
                // Verificar que el contenedor exista y tenga un tamaño válido
                if (container) {
                    renderChart(
                        containerId,
                        response.data.categories,
                        response.data.data
                    );
                } else {
                    console.error(`Contenedor ${containerId} no válido.`);
                }
            });
            // Create the chart
            generando.value = false;
        });
};

const textSubtitulo = {
    todos: "PREINSCRIPCIÓN / INSCRITOS",
    PREINSCRITO: "PREINSCRIPCIÓN",
    INSCRITO: "INSCRITOS",
};

const renderChart = (containerId, categories, data) => {
    const rowHeight = 80;
    const minHeight = 200;
    const calculatedHeight = Math.max(minHeight, categories.length * rowHeight);
    Highcharts.chart(containerId, {
        chart: {
            type: "column",
        },
        title: {
            align: "center",
            text: `INSCRIPCIÓN DE POSTULANTES`,
        },
        subtitle: {
            align: "center",
            text: `${textSubtitulo[form.value.tipoR]}`,
        },
        accessibility: {
            announceNewData: {
                enabled: true,
            },
        },
        xAxis: {
            type: "category",
        },
        yAxis: {
            title: {
                text: "TOTAL",
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                depth: 100,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    // format: "{point.y}",
                    style: {
                        fontSize: "11px",
                        fontWeight: "bold",
                    },
                },
            },
        },
        tooltip: {
            useHTML: true,
            formatter: function () {
                return `
                    <div style="text-align:center;">
                        <div style="display:inline-block; width:12px; height:12px; background:${this.point.color}; border-radius:50%; margin-right:5px;"></div>
                        <strong style="color:${this.point.color};">${this.point.name}</strong>
                        <br>
                        <span class="text-md"><strong>Total:</strong> ${this.point.y}</span>
                    </div>
                    `;
            },
        },

        series: [
            {
                name: "Reporte Inscripción",
                data: data,
                colorByPoint: true,
            },
        ],
    });
};
</script>
<template>
    <Head title="Reporte Gráfico Inscripciones"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gráfico Inscripciones</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Reportes > Gráfico Inscripciones
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
                                    <label class="required">Filtrar</label>
                                    <select
                                        v-model="form.tipoR"
                                        class="form-control"
                                    >
                                        <option
                                            v-for="item in listTipoInscripcion"
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
                                            v-for="item in listUnidads"
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

        <div class="row overflow-auto pb-4" style="max-height: 600px">
            <div class="col-12 mt-3" id="container"></div>
        </div>
    </Content>
</template>
