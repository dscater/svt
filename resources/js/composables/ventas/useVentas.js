import { onMounted, ref } from "vue";

const oVenta = ref({
    id: 0,
    tipo_pago: "EFECTIVO",
    user_id: "",
    fecha: "",
    hora: "",
    status: "",
    detalle_ventas: [],
    _method: "POST",
});

export const useVentas = () => {
    const setVenta = (item = null) => {
        if (item) {
            oVenta.value.id = item.id;
            oVenta.value.tipo_pago = item.tipo_pago;
            oVenta.value.user_id = item.user_id;
            oVenta.value.fecha = item.fecha;
            oVenta.value.hora = item.hora;
            oVenta.value.status = item.status;
            oVenta.value.detalle_ventas = item.detalle_ventas;
            oVenta.value._method = "PUT";
            return oVenta;
        }
        return false;
    };

    const limpiarVenta = () => {
        oVenta.value.id = 0;
        oVenta.value.tipo_pago = "EFECTIVO";
        oVenta.value.user_id = "";
        oVenta.value.fecha = "";
        oVenta.value.hora = "";
        oVenta.value.status = "";
        oVenta.value.detalle_ventas = [];
        oVenta.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oVenta,
        setVenta,
        limpiarVenta,
    };
};
