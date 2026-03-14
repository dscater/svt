<script setup>
import { onMounted, onUnmounted, ref, nextTick } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";
import ItemMenu from "@/Components/ItemMenu.vue";
import { useSideBar } from "@/composables/useSidebar.js";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const { closeSidebar, toggleSubMenuELem } = useSideBar();
const { auth } = usePage().props;
const configuracionStore = useConfiguracionStore();
const appStore = useAppStore();
const usuario = ref(null);
const permisos = ref([]);
const toggleSubMenu = (e) => {
    e.stopPropagation();
    const elem = e.currentTarget;
    if (
        elem.classList.contains("menu-is-opening") &&
        elem.classList.contains("menu-open")
    ) {
        elem.classList.remove("menu-is-opening");
        elem.classList.remove("menu-open");
        toggleSubMenuELem(elem, false);
    } else {
        elem.classList.add("menu-is-opening");
        elem.classList.add("menu-open");
        toggleSubMenuELem(elem, true);
    }
};

const route_current = ref("");
router.on("navigate", (event) => {
    route_current.value = route().current();
    closeSidebar();
});

onMounted(() => {
    configuracionStore.initConfiguracion();
    usuario.value = appStore.getUsuario;
    permisos.value = auth.permisos;
    // Selecciona el elemento del widget
    var sidebarSearchElement = $('[data-widget="sidebar-search"]');
    // Configura manualmente el texto de "no encontrado"
    sidebarSearchElement.data("notFoundText", "Sin resultados");
});

const salir = () => {
    Swal.fire({
        icon: "question",
        title: "Cerrar sesión",
        html: `¿Esta seguro(a) de cerrar sesión?`,
        showCancelButton: true,
        confirmButtonText: "Si, salir",
        cancelButtonText: "Cancelar",
        denyButtonText: `Cancelar`,
        customClass: {
            confirmButton: "btn-success",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            axios
                .post(route("logout"))
                .then((response) => {})
                .finally(() => {
                    window.location.href = "/";
                });
        }
    });
};

onUnmounted(() => {});
</script>
<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-success elevation-4">
        <!-- Brand Logo -->
        <a :href="route('inicio')" class="brand-link">
            <img
                :src="configuracionStore.oConfiguracion.url_logo"
                alt="Logo"
                class="brand-image img-circle elevation-3"
                style="opacity: 0.8"
            />
            <span
                class="brand-text font-weight-light title_Chau_Philomene_One"
                >{{ configuracionStore.oConfiguracion.nombre_sistema }}</span
            >
        </a>

        <!-- Sidebar -->
        <div class="sidebar p-0">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img
                        :src="usuario?.url_foto"
                        class="img-circle elevation-2"
                        alt="User Image"
                    />
                </div>
                <div class="info">
                    <Link :href="route('profile.edit')" class="d-block">{{
                        usuario?.usuario
                    }}</Link>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="false"
                >
                    <ItemMenu
                        :label="'Inicio'"
                        :ruta="'inicio'"
                        :icon="'fa fa-home'"
                    ></ItemMenu>
                    <li
                        class="nav-header font-weight-bold bg-principal"
                        v-if="
                            permisos == '*' ||
                            permisos.includes('usuarios.index')
                        "
                    >
                        ADMINISTRACIÓN
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' || permisos.includes('ventas.index')
                        "
                        :label="'Ventas'"
                        :ruta="'ventas.index'"
                        :icon="'fa fa-list-alt'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('productos.index')
                        "
                        :label="'Productos'"
                        :ruta="'productos.index'"
                        :icon="'fa fa-list'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('usuarios.index')
                        "
                        :label="'Usuarios'"
                        :ruta="'usuarios.index'"
                        :icon="'fa fa-users'"
                    ></ItemMenu>
                    <li class="nav-header font-weight-bold bg-principal">
                        OTROS
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('configuracions.index')
                        "
                        :label="'Configuración Sistema'"
                        :ruta="'configuracions.index'"
                        :icon="'fa fa-cog'"
                    ></ItemMenu>
                    <ItemMenu
                        :label="'Perfil'"
                        :ruta="'profile.edit'"
                        :icon="'fa fa-id-card'"
                    ></ItemMenu>
                    <li class="nav-item">
                        <a
                            href="#"
                            class="nav-link"
                            @click.prevent="salir()"
                            ref="link"
                        >
                            <i class="nav-icon fa fa-power-off"></i>
                            <p>Salir</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>
<style scoped></style>
