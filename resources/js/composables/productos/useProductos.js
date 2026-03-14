import { onMounted, ref } from "vue";

const oProducto = ref({
    id: 0,
    nombre: "",
    foto: "",
    marca: "",
    modelo: "",
    precio: "",
    talla: "",
    _method: "POST",
});

export const useProductos = () => {
    const setProducto = (item = null) => {
        if (item) {
            oProducto.value.id = item.id;
            oProducto.value.nombre = item.nombre;
            oProducto.value.marca = item.marca;
            oProducto.value.modelo = item.modelo;
            oProducto.value.precio = item.precio;
            oProducto.value.talla = item.talla;
            oProducto.value._method = "PUT";
            return oProducto;
        }
        return false;
    };

    const limpiarProducto = () => {
        oProducto.value.id = 0;
        oProducto.value.nombre = "";
        oProducto.value.foto = "";
        oProducto.value.marca = "";
        oProducto.value.modelo = "";
        oProducto.value.precio = "";
        oProducto.value.talla = "";
        oProducto.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oProducto,
        setProducto,
        limpiarProducto,
    };
};
