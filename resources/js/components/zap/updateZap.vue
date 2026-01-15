<template>
    <div>


        <div class="Z2">
            <form @submit.prevent="submitForm">
                <div class="formulaireZap">

                    <div>
                        <h2 class="loginTitre"> Formulaire</h2>
                    </div>
                    <div class="form" style="display: none">
                        <label class="form-label">idZap:</label>
                        <input type="text" class="form-control" v-model="form.id" />
                    </div>

                    <div class="form">
                        <label class="form-label">code ZAP:</label>
                        <input type="text" class="form-control" v-model="form.codeZap" />
                    </div>

                    <div class="form">
                        <label class="form-label">DREN:</label>
                        <input type="text" class="form-control" v-model="form.dren" />
                    </div>

                    <div class="form">
                        <label class="form-label">CISCO:</label>
                        <input type="text" class="form-control" v-model="form.cisco" />
                    </div>

                    <div class="form">
                        <label class="form-label">ZAP:</label>
                        <input type="text" class="form-control" v-model="form.zap" />
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre d'Etablissement:</label>
                        <input type="number" class="form-control" v-model="form.nbrEtab" />
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
    name: "UpdateZap",
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
                codeZap:'',
                dren: '',
                cisco: '',
                zap: '',
                nbrEtab: '',
            },
            loading: true, // Utilisé pour attendre la récupération des données
        };
    },
    created() {
        this.fetchZap();
    },
    methods: {
        fetchZap() {
            fetch(`/api/update-zap/${this.id}`)
                .then(response => response.json())
                .then(data => {
                    this.form = {
                        id: data.id,
                        codeZap: data.codeZap,
                        dren: data.dren,
                        cisco: data.cisco,
                        zap: data.zap,
                        nbrEtab: data.nbrEtab,
                    };
                    console.log("Données récupérées:", this.form); // Vérifie le contenu de form
                    this.loading = false; // Indique que le chargement est terminé
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération du ZAP:", error);
                    this.loading = false;
                });
        },
        submitForm() {
            console.log("Contenu de form avant soumission:", this.form);
            fetch(`/api/update-zap/traitement`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
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
        annuler() {
            this.$emit('close');
        },

    }
}

</script>