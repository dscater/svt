<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { useUsuarios } from "@/composables/usuarios/useUsuarios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
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
const accion_form = ref(props.accion_formulario);
const muestra_form = ref(props.muestra_formulario);
const enviando = ref(false);
let form = useForm(oUsuario.value);
watch(
    () => props.muestra_formulario,
    (newValue) => {
        muestra_form.value = newValue;
        if (muestra_form.value) {
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            form = useForm(oUsuario.value);
            options.value = [
                {
                    value: oUsuario.value.persona_id,
                    label: oUsuario.value.persona
                        ? `${oUsuario.value.persona.full_name} - ${oUsuario.value.persona.ci}`
                        : "Cargando...",
                },
            ];
        } else {
            document
                .getElementsByTagName("body")[0]
                .classList.remove("modal-open");
        }
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

const { flash } = usePage().props;

const listExpedido = [
    { value: "LP", label: "La Paz" },
    { value: "CB", label: "Cochabamba" },
    { value: "SC", label: "Santa Cruz" },
    { value: "CH", label: "Chuquisaca" },
    { value: "OR", label: "Oruro" },
    { value: "PT", label: "Potosi" },
    { value: "TJ", label: "Tarija" },
    { value: "PD", label: "Pando" },
    { value: "BN", label: "Beni" },
    { value: "QR", label: "QR" },
];

const listRoles = ref([]);
const foto = ref(null);

function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];
}

const tituloDialog = computed(() => {
    return accion_form.value == 0
        ? `<i class="fa fa-plus"></i> Nuevo Usuario`
        : `<i class="fa fa-edit"></i> Editar Usuario`;
});

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Enviando...`;
    }
    if (accion_form.value == 0) {
        return `<i class="fa fa-save"></i> Guardar`;
    }
    return `<i class="fa fa-edit"></i> Actualizar`;
});

const enviarFormulario = () => {
    enviando.value = true;
    let url =
        form["_method"] == "POST"
            ? route("usuarios.store")
            : route("usuarios.update", form.id);

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
            limpiarUsuario();
            emits("envio-formulario");
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

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(muestra_form, (newVal) => {
    if (!newVal) {
        emits("cerrar-formulario");
    }
});

const cerrarFormulario = () => {
    muestra_form.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const cargarListas = () => {};

const options = ref([]);
const loading = ref(false);
onMounted(() => {
    cargarListas();
});
</script>

<template>
    <MiModal
        :open_modal="muestra_form"
        @close="cerrarFormulario"
        :size="'modal-xl'"
        :header-class="'bg-principal'"
        :footer-class="'justify-content-end'"
    >
        <template #header>
            <h4 class="modal-title text-white" v-html="tituloDialog"></h4>
            <button
                type="button"
                class="close"
                @click.prevent="cerrarFormulario()"
            >
                <span aria-hidden="true">×</span>
            </button>
        </template>

        <template #body>
            <form @submit.prevent="enviarFormulario()">
                <p class="text-muted text-xs mb-0">
                    Todos los campos con
                    <span class="text-danger">(*)</span> son obligatorios.
                </p>
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <label class="required">Usuario</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.usuario,
                            }"
                            v-model="form.usuario"
                        />
                        <ul
                            v-if="form.errors?.usuario"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.usuario }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label :class="[form.id == 0 ? 'required' : '']"
                            >Contraseña</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.password,
                            }"
                            v-model="form.password"
                        />

                        <ul
                            v-if="form.errors?.password"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.password }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label class="required">Seleccionar Tipo</label>
                        <select
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.tipo,
                            }"
                            v-model="form.tipo"
                        >
                            <option value="">- Seleccione -</option>
                            <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                            <option value="VENDEDOR">VENDEDOR</option>
                        </select>

                        <ul
                            v-if="form.errors?.tipo"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.tipo }}
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-default"
                @click.prevent="cerrarFormulario()"
            >
                Cerrar
            </button>
            <button
                type="button"
                class="btn btn-success"
                :disabled="enviando"
                @click.prevent="enviarFormulario"
                v-html="textBtn"
            ></button>
        </template>
    </MiModal>
</template>
