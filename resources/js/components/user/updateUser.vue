<template>
    <div>


        <div class="Z2O">
            <form @submit.prevent="submitForm">
                <div class="formulaire">
                    <div>
                        <h2 class="loginTitre"> Formulaire</h2>
                    </div>
                    <div class="form" style="display: none">
                        <label class="form-label">idStock:</label>
                        <input type="text" class="form-control" v-model="form.id" />
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
                        <button class="btnAjout">MODIFIER</button>
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
    name: "UpdateUser",
    props: {
        id: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            form: {
                id: '',
                username: '',
                role: '',
                password: '',
                passwordConfirmation: "",
            },
            showPassword: false,
            loading: true, // Utilisé pour attendre la récupération des données
        };
    },
    created() {
        this.fetchUser();
    },
    methods: {
        fetchUser() {
            fetch(`/api/update-user/${this.id}`)
                .then(response => response.json())
                .then(data => {
                    this.form = {
                        id: data.id,
                        username: data.nom,
                        role: data.role,
                        password: data.mdp,
                    };
                    console.log("Données récupérées:", this.form);
                    this.loading = false; // Indique que le chargement est terminé
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération de l'utilisateur:", error);
                    this.loading = false;
                });
        },
        submitForm() {
            if (this.form.password !== this.form.passwordConfirmation) {
                Swal.fire("Erreur", "Les mots de passe ne correspondent pas.", "error");
                return; // Arrête l'exécution si les mots de passe sont différents
            }
            
            console.log("Contenu de form avant soumission:", this.form);
            fetch(`/api/update-user/traitement`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify({
                    id: this.form.id,
                    username: this.form.username,
                    role: this.form.role,
                    password: this.form.password,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        this.statusMessage = data.success;
                        this.errorMessage = "";
                        Swal.fire("Succès", data.success, "success");

                        this.$emit("userAdded");
                        this.$emit('close');
                    } else if (data.error) {
                        this.errorMessage = data.error;
                        this.statusMessage = "";
                        Swal.fire("Erreur", data.error, "error");
                        this.$emit('close');
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
}

</script>