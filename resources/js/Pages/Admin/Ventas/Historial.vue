<script setup>
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useVentas } from "@/composables/ventas/useVentas";
import { useAxios } from "@/composables/axios/useAxios";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
const { props: props_page } = usePage();
const appStore = useAppStore();
onBeforeMount(() => {
    appStore.startLoading();
});

onMounted(() => {
    appStore.stopLoading();
});

const miTable = ref(null);
const headers = [
    {
        label: "NRO.",
        key: "nro_fila",
        sortable: false,
        width: "4%",
    },
    {
        label: "CÓD. VENTA",
        key: "venta.id",
        sortable: true,
        width: "4%",
    },
    {
        label: "CÓDIGO",
        key: "producto.codigo",
        sortable: true,
    },
    {
        label: "NOMBRE DEL PRODUCTO",
        key: "producto.nombre",
        sortable: true,
    },
    {
        label: "MARCA",
        key: "producto.marca",
        sortable: true,
    },
    {
        label: "MODELO",
        key: "producto.modelo",
        sortable: true,
    },
    {
        label: "PRECIO",
        key: "producto.precio",
        sortable: true,
    },
    {
        label: "TALLA",
        key: "producto.talla",
        sortable: true,
    },
    {
        label: "TIPO PAGO",
        key: "venta.tipo_pago",
        sortable: true,
    },
    // {
    //     label: "FOTO",
    //     key: "foto",
    //     sortable: true,
    // },
    {
        label: "FECHA",
        key: "venta.fecha_hora",
        sortable: true,
    },
    {
        label: "USUARIO/ROL",
        key: "usuario",
        sortable: true,
    },
];

const multiSearch = ref({
    search: "",
    fecha_ini: "",
    fecha_fin: "",
    modelo: "",
    usuario: "",
    filtro: [],
});

const imprimirBarras = (id) => {
    const url = route("ventas.barras") + "?venta_id=" + id;

    window.open(url, "_blank");
};
</script>
<template>
    <Head title="Historial de Ventas"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Historial de Ventas</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Historial de Ventas
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-4 my-1">
                <div class="input-group" style="align-items: end">
                    <input
                        v-model="multiSearch.search"
                        placeholder="Buscar"
                        class="form-control border-1 border-right-0"
                    />
                    <div class="input-append">
                        <button
                            class="btn btn-default rounded-0 border-left-0"
                            @click="updateDatos"
                        >
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
                <small
                    class="text-muted font-weight-bold mx-auto d-block w-100 text-center"
                    >Buscar</small
                >
            </div>
            <div class="col-md-2 my-1">
                <input
                    type="date"
                    v-model="multiSearch.fecha_ini"
                    class="form-control"
                />
                <small
                    class="text-muted font-weight-bold mx-auto d-block w-100 text-center"
                    >Fecha inicio</small
                >
            </div>
            <div class="col-md-2 my-1">
                <input
                    type="date"
                    v-model="multiSearch.fecha_fin"
                    class="form-control"
                />
                <small
                    class="text-muted font-weight-bold mx-auto d-block w-100 text-center"
                    >Fecha final</small
                >
            </div>
            <div class="col-md-2 my-1">
                <input
                    type="text"
                    v-model="multiSearch.modelo"
                    placeholder="Modelo"
                    class="form-control"
                />
                <small
                    class="text-muted font-weight-bold mx-auto d-block w-100 text-center"
                    >Modelo</small
                >
            </div>
            <div class="col-md-2 my-1">
                <input
                    type="text"
                    v-model="multiSearch.usuario"
                    placeholder="Usuario"
                    class="form-control"
                />
                <small
                    class="text-muted font-weight-bold mx-auto d-block w-100 text-center"
                    >Usuario</small
                >
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12">
                        <MiTable
                            :tableClass="'bg-white mitabla'"
                            ref="miTable"
                            :cols="headers"
                            :api="true"
                            :url="route('ventas.paginadoHistorial')"
                            :numPages="20"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
                            :header-class="'bg__primary'"
                            fixed-header
                        >
                            <template #usuario="{ item }">
                                <span
                                    >{{ item.venta.user.usuario }} /
                                    {{ item.venta.user.tipo }}</span
                                >
                            </template>
                            <template #codigo="{ item }">
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="Imprimir"
                                    placement="left-start"
                                >
                                    <button
                                        class="btn bg1"
                                        @click="imprimirBarras(item.id)"
                                    >
                                        {{ item.codigo }}
                                        <i
                                            class="fa fa-external-link-alt"
                                        ></i></button
                                ></el-tooltip>
                            </template>
                            <template #foto="{ item }">
                                <img
                                    :src="item.producto.url_foto"
                                    width="90px"
                                />
                            </template>
                        </MiTable>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
