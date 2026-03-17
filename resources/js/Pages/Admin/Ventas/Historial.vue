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

const multiSearch = ref({
    search: "",
    fecha_ini: "",
    fecha_fin: "",
    modelo: "",
    usuario: "",
});

const listRegistros = ref([]);
const getRegistros = () => {
    axios
        .get(route("ventas.paginadoHistorial"), {
            params: multiSearch.value,
        })
        .then((response) => {
            listRegistros.value = response.data.registros;
        });
};

const exportarPDF = () => {
    const params = new URLSearchParams(multiSearch.value).toString();
    const url = route("ventas.exportarPDF") + "?" + params;

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
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4 my-1">
                        <input
                            v-model="multiSearch.search"
                            placeholder="Cód. venta, producto, marca, talla, tipo de pago"
                            class="form-control border-1 border-right-0"
                        />
                        <small
                            class="text-muted font-weight-bold mx-auto d-block w-100 text-center"
                            >Cód. venta, producto, marca, talla, tipo de
                            pago</small
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
                </div>
            </div>
            <div class="col-12 text-center my-2">
                <button class="btn btn-primary" @click="getRegistros">
                    Buscar <i class="fa fa-search"></i>
                </button>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body overflow-auto">
                        <button
                            class="btn btn-success"
                            :disabled="listRegistros.length == 0"
                            @click="exportarPDF"
                        >
                            Exportar <i class="fa fa-external-link-alt"></i>
                        </button>
                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <tr>
                                    <th>N°</th>
                                    <th>CÓD. VENTA</th>
                                    <th>CÓDIGO PRODUCTO</th>
                                    <th>NOMBRE PRODUCTO</th>
                                    <th>MARCA</th>
                                    <th>MODELO</th>
                                    <th>PRECIO</th>
                                    <th>TALLA</th>
                                    <th>TIPO DE PAGO</th>
                                    <th>FECHA Y HORA</th>
                                    <th>USUARIO/ROL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listRegistros">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.venta.id }}</td>
                                    <td>{{ item.producto.codigo }}</td>
                                    <td>{{ item.producto.nombre }}</td>
                                    <td>{{ item.producto.marca }}</td>
                                    <td>{{ item.producto.modelo }}</td>
                                    <td>{{ item.producto.precio }}</td>
                                    <td>{{ item.producto.talla }}</td>
                                    <td>{{ item.venta.tipo_pago }}</td>
                                    <td>{{ item.venta.fecha_hora }}</td>
                                    <td>
                                        {{ item.venta.user.usuario }}/{{
                                            item.venta.user.tipo
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
