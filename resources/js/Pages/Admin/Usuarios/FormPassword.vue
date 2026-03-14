<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useUsuarios } from "@/composables/usuarios/useUsuarios";
import { watch, ref, computed, defineEmits } from "vue";
const props = defineProps({
    muestra_formulario: {
        type: Boolean,
        default: false,
    },
    accion_formulario: {
        type: Number,
        default: 0,
    },
});

const { oUsuario, limpiarUsuario } = useUsuarios();
const accion = ref(props.accion_formulario);
const formulario = ref(props.muestra_formulario);
let form = useForm({
    password: "",
});
watch(
    () => props.muestra_formulario,
    (newValue) => {
        formulario.value = newValue;

        document.getElementsByTagName("body")[0].classList.add("modal-open");
    }
);
watch(
    () => props.accion_formulario,
    (newValue) => {
        accion.value = newValue;
    }
);

const { flash } = usePage().props;

const tituloFormulario = computed(() => {
    return accion.value == 0 ? `Agregar Usuario` : `Actualizar Contraseña`;
});

const enviarFormulario = () => {
    let url = route("usuarios.password", oUsuario.value.id);

    form.put(url, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            form.password = "";
            limpiarUsuario();
            emits("envio-formulario");
        },
        onError: (err) => {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(formulario, (newVal) => {
    if (!newVal) {
        emits("cerrar-formulario");
    }
});

const cerrarFormulario = () => {
    formulario.value = false;
};
</script>

<template>
    <div
        class="modal fade"
        :class="{
            show: formulario,
        }"
        id="modal-formulario-form"
        :style="{
            display: formulario ? 'block' : 'none',
        }"
    >
        <div class="modal-formulario modal-lg mx-auto">
            <div class="modal-content">
                <div class="modal-header bg-principal text-white">
                    <h4
                        class="modal-title text-white"
                        v-html="tituloFormulario"
                    ></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarFormulario()"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="enviarFormulario()">
                        <div class="row">
                            <div class="px-4 text-center col-md-12">
                                <span class="text-body-2 h3"
                                    >{{ oUsuario.nombre }}
                                    {{ oUsuario.paterno }}
                                    {{ oUsuario.materno }}</span
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Ingresa la nueva contraseña:</label>
                                <input
                                    placeholder="Ingresa la nueva contraseña"
                                    class="form-control"
                                    autocomplete="false"
                                    v-model="form.password"
                                    type="password"
                                />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a
                        href="javascript:;"
                        class="btn btn-default"
                        @click="cerrarFormulario()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                    <button
                        type="button"
                        @click="enviarFormulario()"
                        class="btn btn-success"
                    >
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
