import { onMounted, ref } from "vue";

const oUsuario = ref({
    id: 0,
    nombre: "",
    paterno: "",
    materno: "",
    dir: "",
    ci: "",
    ci_exp: "",
    fono: "",
    correo: "",
    usuario: "",
    password: "",
    acceso: "" + 0,
    role_id: "",
    foto: "",
    _method: "POST",
});

export const useUsuarios = () => {
    const setUsuario = (item = null, ver = false) => {
        if (item) {
            oUsuario.value.id = item.id;
            oUsuario.value.nombre = item.nombre;
            oUsuario.value.paterno = item.paterno;
            oUsuario.value.materno = item.materno;
            oUsuario.value.dir = item.dir;
            oUsuario.value.ci = item.ci;
            oUsuario.value.ci_exp = item.ci_exp;
            oUsuario.value.fono = item.fono;
            oUsuario.value.correo = item.correo;
            oUsuario.value.usuario = item.usuario;
            oUsuario.value.password = item.password;
            oUsuario.value.acceso = "" + item.acceso;
            oUsuario.value.role_id = item.role_id;
            oUsuario.value.foto = "";
            oUsuario.value.persona = item.persona;
            oUsuario.value._method = "PUT";
            return oUsuario;
        }
        return false;
    };

    const limpiarUsuario = () => {
        oUsuario.value.id = 0;
        oUsuario.value.nombre = "";
        oUsuario.value.paterno = "";
        oUsuario.value.materno = "";
        oUsuario.value.dir = "";
        oUsuario.value.ci = "";
        oUsuario.value.ci_exp = "";
        oUsuario.value.fono = "";
        oUsuario.value.correo = "";
        oUsuario.value.usuario = "";
        oUsuario.value.password = "";
        oUsuario.value.acceso = "" + 0;
        oUsuario.value.role_id = "";
        oUsuario.value.foto = "";
        oUsuario._method = "POST";
    };

    onMounted(() => {});

    return {
        oUsuario,
        setUsuario,
        limpiarUsuario,
    };
};
