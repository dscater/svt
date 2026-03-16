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

const { setVenta, limpiarVenta } = useVentas();
const { axiosDelete } = useAxios();

const miTable = ref(null);
const headers = [
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
    {
        label: "FOTO",
        key: "foto",
        sortable: true,
    },
    {
        label: "FECHA",
        key: "venta.fecha_hora",
        sortable: true,
    },
    {
        label: "ACCIÓN",
        key: "accion",
        fixed: "right",
        width: "4%",
    },
];

const multiSearch = ref({
    search: "",
    filtro: [],
});

const accion_formulario = ref(0);
const muestra_formulario = ref(false);

const agregarRegistro = () => {
    limpiarVenta();
    accion_formulario.value = 0;
    muestra_formulario.value = true;
};

const imprimirBarras = (id) => {
    const url = route("ventas.barras") + "?venta_id=" + id;

    window.open(url, "_blank");
};

const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        muestra_formulario.value = false;
    }
};

const eliminarVenta = (item) => {
    Swal.fire({
        title: "¿Quierés anular este registro?",
        html: `<strong>Código Venta: ${item.venta.id}</strong><br/><strong>Total Productos: ${item.venta.total_productos}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, anular",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "btn-danger",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("ventas.destroy", item.venta.id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};
</script>
<template>
    <Head title="Ventas"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Ventas</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Ventas</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <Link
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'ventas.create',
                                )
                            "
                            class="btn btn-success"
                            :href="route('ventas.create')"
                        >
                            <i class="fa fa-plus"></i> Nueva Venta
                        </Link>
                        <button
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'ventas.barras',
                                )
                            "
                            class="btn bg1 ml-1"
                            @click="imprimirBarras('todos')"
                        >
                            <i class="fa fa-barcode"></i> Imprimir Todos los
                            Códigos de Barra
                        </button>
                    </div>
                    <div class="col-md-8 my-1">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div
                                    class="input-group"
                                    style="align-items: end"
                                >
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <MiTable
                            :tableClass="'bg-white mitabla'"
                            ref="miTable"
                            :cols="headers"
                            :api="true"
                            :url="route('ventas.paginado')"
                            :numPages="5"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
                            :header-class="'bg__primary'"
                            fixed-header
                        >
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
                            <template #accion="{ item }">
                                <!-- <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'ventas.edit',
                                        )
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Editar"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-warning"
                                            @click="
                                                setVenta(item);
                                                accion_formulario = 1;
                                                muestra_formulario = true;
                                            "
                                        >
                                            <i class="fa fa-pen"></i></button
                                    ></el-tooltip>
                                </template> -->

                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'ventas.destroy',
                                        )
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Anular"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-danger"
                                            @click="eliminarVenta(item)"
                                        >
                                            <i class="fa fa-ban"></i></button
                                    ></el-tooltip>
                                </template>
                            </template>
                        </MiTable>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
