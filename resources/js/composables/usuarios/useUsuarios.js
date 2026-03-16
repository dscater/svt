import { onMounted, ref } from "vue";

const oUsuario = ref({
    id: 0,
    usuario: "",
    password: "",
    tipo: "",
    _method: "POST",
});

export const useUsuarios = () => {
    const setUsuario = (item = null, ver = false) => {
        if (item) {
            oUsuario.value.id = item.id;
            oUsuario.value.usuario = item.usuario;
            oUsuario.value.password = item.password;
            oUsuario.value.tipo = item.tipo;
            oUsuario.value._method = "PUT";
            return oUsuario;
        }
        return false;
    };

    const limpiarUsuario = () => {
        oUsuario.value.id = 0;
        oUsuario.value.usuario = "";
        oUsuario.value.password = "";
        oUsuario.value.tipo = "";
        oUsuario._method = "POST";
    };

    onMounted(() => {});

    return {
        oUsuario,
        setUsuario,
        limpiarUsuario,
    };
};
