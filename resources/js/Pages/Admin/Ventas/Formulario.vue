<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useVentas } from "@/composables/ventas/useVentas";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import axios from "axios";
// TOAST
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Show from "../Qrs/Show.vue";
const props = defineProps({
    venta: {
        type: Object,
        default: null,
    },
    accion_formulario: {
        type: Number,
        default: 0,
    },
});

const { oVenta, setVenta, limpiarVenta } = useVentas();
const accion_form = ref(props.accion_formulario);
const muestra_form = ref(props.muestra_formulario);
const enviando = ref(false);
let form = useForm(props.venta);
watch(
    () => props.venta,
    (newValue) => {
        form = useForm(newValue);
        setVenta(newValue);
    },
);
watch(
    () => props.accion_formulario,
    (newValue) => {
        accion_form.value = newValue;
        if (accion_form.value == 0) {
            form["_method"] = "POST";
        }
    },
);

const enviarFormulario = () => {
    enviando.value = true;
    let url =
        accion_form.value == 0
            ? route("ventas.store")
            : route("ventas.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
            form.reset();
            limpiarVenta();
        },
        onError: (err, code) => {
            console.log(code ?? "");
            console.log(form.errors);
            if (form.errors) {
                const error =
                    "Existen errores en el formulario, por favor verifique";
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    html: `<strong>${error}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            } else {
                const error =
                    "Ocurrió un error inesperado contactese con el Administrador";
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    html: `<strong>${error}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            }
            console.log("error: " + err.error);
        },
        onFinish: () => {
            enviando.value = false;
        },
    });
};

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Enviando...`;
    }
    if (accion_form.value == 0) {
        return `<i class="fa fa-save"></i> Registrar venta`;
    }
    return `<i class="fa fa-edit"></i> Actualizar Venta`;
});

const inputSeleccionado = ref(false);
const codigoProducto = ref("");
const agregarProducto = () => {
    axios
        .get(route("productos.byCodigo"), {
            params: {
                codigo: codigoProducto.value,
            },
        })
        .then((response) => {
            // verificar que no exista ya en la lista
            const existe = form.detalle_ventas.some(
                (item) => item.producto_id === response.data.id,
            );

            if (!existe) {
                form.detalle_ventas.push({
                    producto_id: response.data.id,
                    producto: response.data,
                });
                toast.success(
                    `Producto ${response.data.nombre} cargado correctamente`,
                );
            } else {
                toast.info(`Producto ${response.data.nombre} ya fue agregado`);
            }
        })
        .catch((error) => {
            // mostrar error
            console.log(error);
            if (error.response && error.response.data.errors.error) {
                toast.error(`${error.response.data.errors.error[0]}`);
            } else {
                toast.error(`Ocurrió un error al intentar obtener el registro`);
            }
        })
        .finally(() => {
            codigoProducto.value = "";
        });
};

const quitar = (index) => {
    form.detalle_ventas.splice(index, 1);
};
const totalVenta = computed(() => {
    return form.detalle_ventas.reduce((total, item) => {
        const precio = item.producto?.precio;

        if (precio !== null && precio !== undefined && precio !== "") {
            return total + Number(precio);
        }

        return total;
    }, 0);
});

const muestra_qr = ref(false);

onMounted(() => {});
</script>

<template>
    <form @submit.prevent="enviarFormulario()">
        <div class="row">
            <div class="col-12 pb-0">
                <div class="input-group mb-0">
                    <div class="input-group-prepend">
                        <span
                            class="input-group-text bg-white"
                            for="inputCodigo"
                            :class="{
                                seleccionado: inputSeleccionado,
                            }"
                        >
                            <i class="fa fa-barcode"></i>
                        </span>
                    </div>
                    <input
                        type="text"
                        class="form-control"
                        id="inputCodigo"
                        :class="{
                            seleccionado: inputSeleccionado,
                        }"
                        v-model="codigoProducto"
                        @keypress.enter.prevent="agregarProducto"
                        @focus="inputSeleccionado = true"
                        @blur="inputSeleccionado = false"
                    />
                </div>
                <small class="text-muted text-xs font-weight-bold"
                    >Mantener seleccionado el campo y escanear el código de
                    barras</small
                >
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-12 overflow-auto">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>CÓDIGO</th>
                            <th>NOMBRE</th>
                            <th>MARCA</th>
                            <th>MODELO</th>
                            <th>PRECIO</th>
                            <th>TALLA</th>
                            <th>FOTO</th>
                            <th width="4%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="form.detalle_ventas.length > 0">
                            <tr v-for="(item, index) in form.detalle_ventas">
                                <td>{{ item.producto.codigo }}</td>
                                <td>{{ item.producto.nombre }}</td>
                                <td>{{ item.producto.marca }}</td>
                                <td>{{ item.producto.modelo }}</td>
                                <td>{{ item.producto.precio }}</td>
                                <td>{{ item.producto.talla }}</td>
                                <td>
                                    <img
                                        :src="item.producto.url_foto"
                                        alt="Foto"
                                        width="90px"
                                    />
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-danger"
                                        @click="quitar(index)"
                                    >
                                        X
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    NO SE AGREGÓ NINGÚN PRODUCTO
                                </td>
                            </tr>
                        </template>
                        <template v-if="form.detalle_ventas.length > 0">
                            <tr>
                                <td
                                    class="text-right font-weight-bold text-lg"
                                    colspan="4"
                                >
                                    TOTAL
                                </td>
                                <td class="font-weight-bold text-lg">
                                    {{ totalVenta }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <label>Tipo de Pago:</label>
                <div class="row">
                    <div class="col-12">
                        <div class="input-group">
                            <select
                                v-model="form.tipo_pago"
                                class="form-control"
                            >
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="QR">QR</option>
                            </select>
                            <div
                                class="input-group-append"
                                v-if="form.tipo_pago == 'QR'"
                            >
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    @click="muestra_qr = true"
                                >
                                    <i class="fa fa-qrcode"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-2">
                <button
                    type="button"
                    class="btn btn-success"
                    :disabled="enviando"
                    @click.prevent="enviarFormulario"
                    v-html="textBtn"
                ></button>
            </div>
        </div>
        <Show
            :accion_formulario="0"
            :muestra_formulario="muestra_qr"
            @cerrar-formulario="muestra_qr = false"
        ></Show>
    </form>
</template>
<style scoped>
span.seleccionado,
input.seleccionado {
    background-color: var(--bg4) !important;
}
</style>
