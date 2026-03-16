import axios from "axios";
import { defineStore } from "pinia";
export const useQrStore = defineStore("qr", {
    state: () => ({
        oQr: {
            qr: "",
            remitente: "",
            fecha_vencimiento: "",
            _method: "put",
        },
    }),
    actions: {
        initQr() {
            axios
                .get(route("qrs.getQr"))
                .then((response) => {
                    this.setQr(response.data.qr);
                })
                .catch((error) => {
                    console.log("Error al cargar la configuración");
                })
                .finally(() => {
                    console.log("Configuración cargada");
                });
        },
        setQr(value) {
            this.oQr = { ...value };
        },
    },
    getters: {
        getQr() {
            return this.oQr;
        },
    },
});
