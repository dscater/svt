import { useForm } from "@inertiajs/vue3";
export const useFardos = () => {
    const initialState = {
        id: 0,
        nombre: "",
        tipo_venta: "POR UNIDADES",
        precio: 0,
        codigo_barras: "",
        stock: "",
        fecha_registro: "",
        hora_registro: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setFardo = (item = null) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarFardo = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    return {
        form,
        setFardo,
        limpiarFardo,
    };
};
