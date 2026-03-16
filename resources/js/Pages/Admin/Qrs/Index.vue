<script setup>
import App from "@/Layouts/App.vue";
defineOptions({
    layout: App,
});
import Content from "@/Components/Content.vue";
import { onMounted, ref, computed, onBeforeMount } from "vue";
import { usePage, Head, useForm } from "@inertiajs/vue3";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useQrStore } from "@/stores/qr/qrStore";
const appStore = useAppStore();
const qrStore = useQrStore();

onBeforeMount(() => {
    appStore.startLoading();
});

const props_page = defineProps({
    qr: {
        type: Object,
        default: null,
    },
    estado: {
        type: String,
        default: "NO SE CARGÓ NINGÚN QR",
    },
    estado_sw: {
        type: Boolean,
        default: false,
    },
});
const { props } = usePage();
const enviando = ref(false);

let form = useForm({
    id: props_page.qr.id,
    qr: props_page.qr.qr,
    url_qr: props_page.qr.url_qr,
    remitente: props_page.qr.remitente,
    fecha_vencimiento: props_page.qr.fecha_vencimiento,
    _method: "PUT",
});

const enviarFormulario = () => {
    form["_method"] = "put";
    enviando.value = true;
    form.post(route("qrs.update", form.id), {
        onSuccess: (response) => {
            // Mostrar mensaje de éxito
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            limpiaRefs();
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
        },
        onError: (err, code) => {
            console.log(code ?? "");
            const error =
                "Ocurrió un error inesperado contactese con el Administrador";
            Swal.fire({
                icon: "error",
                title: "Error",
                html: `<strong>${error}</strong>`,
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                customClass: {
                    confirmButton: "btn-error",
                },
            });
            console.log("error: " + err.error);
        },
        onFinish: () => {
            enviando.value = false;
            qrStore.initQr();
        },
    });
};
const qr = ref(null);
function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];

    // Generar la URL del archivo cargado
    const fileUrl = URL.createObjectURL(form[key]);
    form["url_" + key] = fileUrl;
}
function limpiaRefs() {
    qr.value = null;
}

const btnGuardar = computed(() => {
    if (enviando.value) {
        return `Guardando... <i class="fa fa-spinner fa-spin"></i>`;
    }

    return `Guardar cambios <i class="fa fa-save"></i>`;
});

onMounted(() => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="QR"></Head>
    <Content>
        <form @submit.prevent="enviarFormulario()">
            <div class="row">
                <div class="col-12 mt-3" v-if="props_page.estado_sw == false">
                    <div class="alert alert-danger">
                        {{ props_page.estado }}
                    </div>
                </div>
                <div class="col-md-4 form-group mb-3">
                    <label
                        class="required"
                        v-if="props.auth.user.permisos.includes('qrs.edit')"
                        >QR</label
                    >
                    <input
                        v-if="props.auth.user.permisos.includes('qrs.edit')"
                        type="file"
                        class="form-control"
                        @change="cargaArchivo($event, 'qr')"
                        ref="qr"
                    />
                    <div class="qr_muestra w-100 text-center">
                        <img
                            :src="form.url_qr"
                            alt=""
                            v-if="form.url_qr"
                            width="80%"
                        />
                    </div>
                    <span class="text-danger" v-if="form.errors?.qr">{{
                        form.errors.qr
                    }}</span>
                </div>
                <div
                    class="col-md-4 form-group mb-3"
                    v-if="props.auth.user.permisos.includes('qrs.edit')"
                >
                    <label class="required">Nombre del remitente</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="form.remitente"
                    />
                    <span class="text-danger" v-if="form.errors?.remitente">{{
                        form.errors.remitente
                    }}</span>
                </div>
                <div
                    class="col-md-4 form-group mb-3"
                    v-if="props.auth.user.permisos.includes('qrs.edit')"
                >
                    <label class="required">Fecha de vencimiento</label>
                    <input
                        type="date"
                        class="form-control"
                        v-model="form.fecha_vencimiento"
                    />
                    <span
                        class="text-danger"
                        v-if="form.errors?.fecha_vencimiento"
                        >{{ form.errors.fecha_vencimiento }}</span
                    >
                </div>
            </div>
            <div
                class="row pb-5"
                v-if="props.auth.user.permisos.includes('qrs.edit')"
            >
                <div
                    class="col-12"
                    v-if="
                        props.auth.user.permisos == '*' ||
                        props.auth.user.permisos.includes('qrs.edit')
                    "
                >
                    <button
                        type="submit"
                        class="btn btn-primary"
                        v-html="btnGuardar"
                        :disabled="enviando"
                    ></button>
                </div>
            </div>
        </form>
    </Content>
</template>
<style scoped>
.qr_muestra {
    margin-top: 10px;
    width: 100%;
    text-align: center;
}
.qr_muestra img {
    max-width: 100%;
}
</style>
