<template>
    <div>


        <div class="Z2">
            <form @submit.prevent="submitForm">
                <div class="formulaireZap">
                    <div>
                        <h2 class="loginTitre"> Formulaire</h2>
                    </div>
                    <div class="form">
                        <label class="form-label">code ZAP:</label>
                        <input type="text" class="form-control" v-model="form.codeZap" required />
                    </div>
                    <div class="form">
                        <label class="form-label">DREN:</label>
                        <input type="text" class="form-control" v-model="form.dren" required />
                    </div>

                    <div class="form">
                        <label class="form-label">CISCO:</label>
                        <input type="text" class="form-control" v-model="form.cisco" required />
                    </div>

                    <div class="form">
                        <label class="form-label">ZAP:</label>
                        <input type="text" class="form-control" v-model="form.zap" required />
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre d'Etablissement:</label>
                        <input type="number" class="form-control" v-model="form.nbrEtab" required />
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
    name: "AjoutZap",
    data() {
        return {
            form: {
                codeZap: "508",
                dren: "ANTSINANANA",
                cisco: "BRICKAVILLE",
                zap: "",
                nbrEtab: "0",
            },
            status: null,
        };
    },
    methods: {
        submitForm() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error("CSRF token not found.");
                Swal.fire({
                    title: "Erreur",
                    text: "CSRF token manquant. Veuillez recharger la page.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
                return;
            }

            fetch("/api/ajout-zap/traitement", {
                method: "POST", // Assurez-vous que cette méthode est POST, comme dans la définition de route
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
                },
                body: JSON.stringify(this.form),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        this.statusMessage = data.success;
                        this.errorMessage = "";
                        Swal.fire("Succès", data.success, "success");

                        this.$emit("userAdded");
                        this.$emit('close');
                        this.resetForm();
                    } else if (data.error) {
                        this.errorMessage = data.error;
                        this.statusMessage = "";
                        Swal.fire("Erreur", data.error, "error");
                        this.$emit('close');
                        this.resetForm();
                    }
                })
                .catch((error) => console.error("Erreur:", error));
        },
        resetForm() {
            this.form = { dren: "", cisco: "", zap: "", nbrEtab: "" };
            this.status = null;
        },

        annuler() {
            this.$emit('close');
        },
    },
};
</script>

<style scoped>
/* Ajoutez des styles personnalisés ici si nécessaire */
</style>


<style>
/* Ajoutez vos styles ici */
</style>
