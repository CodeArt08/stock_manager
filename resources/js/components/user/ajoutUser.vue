<template>
    <div>
        <div class="Z2O">
            <form @submit.prevent="submitForm">
                <div class="formulaire">
                    <div>
                        <h2 class="loginTitre"> Formulaire</h2>
                    </div>
                    <div class="best">
                        <label for="username" class="loginLabel">Nom de l'utilisateur</label>
                        <div class="mdp">
                            <img src="/images/logos/user.png" width="17px" height="17px" />
                            <input type="text" id="username" class="loginInput" placeholder="Type your username"
                                v-model="form.username" required />
                        </div>
                    </div>

                    <div class="best">
                        <label for="userrole" class="loginLabel">Rôle de l'utilisateur</label>
                        <div class="mdp">
                            <input type="text" id="role" class="loginInput" placeholder="Type your role"
                                v-model="form.role" required />
                        </div>
                    </div>


                    <div class="best">
                        <label for="password" class="loginLabel">Mot de passe</label>
                        <div class="mdp2">
                            <img src="/images/logos/key.png" width="14px" height="14px" />
                            <input :type="showPassword ? 'text' : 'password'" id="password" class="loginInput"
                                placeholder="Type your password" v-model="form.password" required />
                        </div>
                    </div>

                    <div class="best">
                        <label for="password-confirm" class="loginLabel">Confirmer le mot de passe</label>
                        <div class="mdp2">
                            <img src="/images/logos/key.png" width="14px" height="14px" />
                            <input :type="showPassword ? 'text' : 'password'" id="password-confirm" class="loginInput"
                                placeholder="Re-enter your password" v-model="form.passwordConfirmation" required />
                        </div>
                    </div>

                    <div class="bestOfone">
                        <input type="checkbox" id="show-password" v-model="showPassword" />
                        <label for="show-password" class="show">Show Password</label>
                    </div>


                    <div class="bestOf">
                        <button class="btnAjout">AJOUTER</button>
                    </div>

                    <div class="end2" @click="annuler">
                        <span>Annuler</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>


<script>
import Swal from 'sweetalert2';
export default {
    name: "AjoutUser",
    data() {
        return {
            form: {
                username: "",
                password: "",
                role: "",
                passwordConfirmation: "",
            },
            showPassword: false,
        };
    },
    methods: {
        submitForm() {
            // Vérifiez si les mots de passe correspondent
            if (this.form.password !== this.form.passwordConfirmation) {
                Swal.fire("Erreur", "Les mots de passe ne correspondent pas.", "error");
                return; // Arrête l'exécution si les mots de passe sont différents
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error("CSRF token not found.");
                return;
            }

            fetch("/api/ajout-user/traitement", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
                },
                body: JSON.stringify({
                    username: this.form.username,
                    password: this.form.password,
                    role: this.form.role,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire("Succès", data.success, "success");
                        this.$emit("userAdded");
                        this.$emit("close");
                    } else if (data.error) {
                        this.errorMessage = data.error;
                        this.statusMessage = "";
                        Swal.fire("Erreur", data.error, "error");
                        this.$emit('close');
                        this.resetForm();
                    }
                })
                .catch((error) => {
                    this.$emit('close');
                    console.error("Erreur:", error)
                });
        },

        annuler() {
            this.$emit('close');
        },
    }

};
</script>

<style>
/* Ajoutez vos styles ici */
</style>