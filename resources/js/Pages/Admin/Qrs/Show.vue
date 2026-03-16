<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
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

const accion_form = ref(props.accion_formulario);
const muestra_form = ref(props.muestra_formulario);
watch(
    () => props.muestra_formulario,
    (newValue) => {
        muestra_form.value = newValue;
        if (muestra_form.value) {
            cargarQr();
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
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

const tituloDialog = computed(() => {
    return accion_form.value == 0
        ? `<i class="fa fa-plus"></i> Ver QR`
        : `<i class="fa fa-edit"></i> Ver QR`;
});

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(muestra_form, (newVal) => {
    if (!newVal) {
        emits("cerrar-formulario");
    }
});

const qr = ref(null);
const estado = ref("NO SE CARGÓ NINGÚN QR");
const estado_sw = ref(false);

const cargarQr = () => {
    axios.get(route("qrs.getQr")).then((response) => {
        qr.value = response.data.qr;
        estado.value = response.data.estado;
        estado_sw.value = response.data.estado_sw;
    });
};

const cerrarFormulario = () => {
    muestra_form.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

onMounted(() => {});
</script>

<template>
    <MiModal
        :open_modal="muestra_form"
        @close="cerrarFormulario"
        :size="'modal-md'"
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
            <div class="row">
                <div class="col-12" v-if="qr">
                    <img :src="qr.url_qr" alt="QR" width="100%" />

                    <div class="col-12 mt-3" v-if="estado_sw == false">
                        <div class="alert alert-danger">
                            {{ estado }}
                        </div>
                    </div>
                    <p class="text-lg mt-3">
                        <b>Remitente: </b>{{ qr.remitente }}
                    </p>
                    <p class="text-lg">
                        <b>Fecha de vencimiento: </b
                        >{{ qr.fecha_vencimiento_t }}
                    </p>
                </div>
            </div>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-default"
                @click.prevent="cerrarFormulario()"
            >
                Cerrar
            </button>
        </template>
    </MiModal>
</template>
