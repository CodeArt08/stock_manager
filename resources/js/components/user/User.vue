<template>
    <div :class="{ 'blur-background': isModalVisible || isModalVisible2 }">
        <div class="Z2user">

            <div class="formulaireUser">
                <div>
                    <h2 class="loginTitreUser">Utilisateurs</h2>
                </div>

                <button @click="openModal" class="ajoutkuser">
                    <img src="/images/logos/plusvrai.png" width="25px" height="25px" />
                    <span class="ajtk">AJOUTER</span>
                </button>

                <table class="tableUser">
                    <thead class="head">
                        <tr>
                            <th><span>Utilisateur</span></th>
                            <th><span>Rôle</span></th>
                            <th><span>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="body">
                        <tr v-for="user in users" :key="user.id" class="tr">
                            <td>{{ user.nom }}</td>
                            <td>{{ user.role }}</td>
                            <td>
                                <div class="action">
                                    <a @click="openModal2(user.id)">
                                        <img src="/images/logos/edit.png" alt="" width="30px" height="30px" />
                                    </a>
                                    <a class="delete" @click="confirmDelete(user.id)">
                                        <img src="/images/logos/delete.png" alt="" width="30px" height="30px" />
                                    </a>
                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>

    </div>

    <div v-if="isModalVisible" class="modal-overlay">
        <ajout-user @close="closeModal" @userAdded="onUserAdded" />
    </div>

    <div v-if="isModalVisible2" class="modal-overlay">
        <update-user :id="selectedUserId" @close="closeModal2" @userAdded="onUserAdded" />
    </div>

</template>

<script>
import Swal from 'sweetalert2';
import AjoutUser from './ajoutUser.vue';
import UpdateUser from './updateUser.vue';

export default {
    components: {
        AjoutUser,
        UpdateUser,
    },
    data() {
        return {
            users: [],
            isModalVisible: false,
            isModalVisible2: false,
            selectedUserId: null,
            currentUserRole: "", // Stocker le nom de l'utilisateur connecté
        };
    },
    created() {
        this.fetchUsers();
        this.getCurrentUser();
    },
    methods: {
        getCurrentUser() {
            // Récupérez le nom d'utilisateur connecté depuis localStorage ou une API
            const storedUser = localStorage.getItem('user');
            if (storedUser) {
                const user = JSON.parse(storedUser);
                this.currentUserRole = user.role; // Supposons que le nom est stocké dans `user.nom`
            } else {
                this.currentUserRole = ""; // Valeur par défaut si aucun utilisateur n'est connecté
            }
        },

        onUserAdded() {
            this.fetchUsers();
        },

        openModal() {
            if (this.currentUserRole === "Administrateur") {
                this.isModalVisible = true;
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Accès refusé",
                    text: "Vous devez être Administrateur pour ajouter un utilisateur.",
                });
            }
        },

        closeModal() {
            this.isModalVisible = false;
        },

        openModal2(userId) {
            if (this.currentUserRole === "Administrateur") {
                this.selectedUserId = userId;
                this.isModalVisible2 = true;
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Accès refusé",
                    text: "Vous devez être Administrateur pour modifier un utilisateur.",
                });
            }
        },

        closeModal2() {
            this.isModalVisible2 = false;
            this.selectedUserId = null;
        },

        fetchUsers(pageUrl = '/api/users') {
            fetch(pageUrl)
                .then((response) => response.json())
                .then((data) => {
                    this.users = data.users.data || [];
                })
                .catch((error) =>
                    console.error("Erreur lors de la récupération des utilisateurs:", error)
                );
        },

        confirmDelete(userId) {
            if (this.currentUserRole === "Administrateur") {
                Swal.fire({
                    title: "Êtes-vous sûr?",
                    text: "Voulez-vous vraiment supprimer cet utilisateur?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Oui, supprimer!",
                    cancelButtonText: "Annuler",
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/api/delete-user/${userId}`, { method: "DELETE" })
                            .then(() => {
                                this.users = this.users.filter((user) => user.id !== userId);
                                Swal.fire("Supprimé!", "L'utilisateur a été supprimé.", "success");
                                this.fetchUsers(); // Recharge la liste des utilisateurs
                            })
                            .catch((error) => {
                                console.error(
                                    "Erreur lors de la suppression de l'utilisateur:",
                                    error
                                );
                                Swal.fire("Erreur", "Impossible de supprimer l'utilisateur.", "error");
                            });
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Accès refusé",
                    text: "Vous devez être Administrateur pour supprimer un utilisateur.",
                });
            }
        },
    },
};
</script>

