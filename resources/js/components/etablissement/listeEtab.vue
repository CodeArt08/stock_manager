<template>
    <div class="yes">
        <router-link to="/liste-etab">
            <button class="retour">
                <img src="/images/logos/arrow.png" width="30px" height="30px" class="fleche" />
                <span class="ajtR">Retour</span>
            </button>
        </router-link>
        <form @submit.prevent="searchEtab" class="formsearch" role="search">
            <input v-model="searchTerm" type="text" placeholder="Code Etablissement" class="inputsearch" />
            <button class="btnsearch" type="submit">
                <img src="/images/logos/magnifying-glass.png" alt="" width="20px" height="20px" />
            </button>
        </form>
        <button v-if="isSearchMode" @click="showAllEtabs" class="affiche2">Afficher tout</button>
    </div>

    <div class="FormEtab">
        <form @submit.prevent="submitForm">
            <div class="tab">
                <div style="display: none;">
                    <label class="form-label2">id :</label>
                    <input type="text" class="form-control2" v-model="form.id" required readonly />
                </div>
                <div  style="display: none;">
                    <label class="form-label2">idZap :</label>
                    <input type="text" class="form-control3" v-model="form.idZap" required readonly />
                </div>
                <div>
                    <label class="form-label2">Code :</label>
                    <input type="text" v-model="form.code" required class="form-control2" />
                </div>
                <div>
                    <label class="form-label2">Nom de l'Etablissement :</label>
                    <input type="text" v-model="form.etab" required class="form-control2" />
                </div>
                <div>
                    <label class="form-label2">Téléphone :</label>
                    <input type="text" v-model="form.telephone" required class="form-control2" />
                </div>
            </div>
            <br>
            <button v-if="!isEditMode" type="submit" class="bt">Ajouter</button>
            <button v-else type="button" class="bt" @click="submitForm">Modifier</button>
            <button v-if="isEditMode" type="button" class="bt2" @click="cancelEdit">Annuler la modification</button>
        </form>
    </div>
    <br><br>
    <div class="bad2">
        <h3>Liste des Etablissements du ZAP: {{ nom }}</h3>
        <table class="tableEtab">
            <thead>
                <tr>
                    <th><span>ZAP</span></th>
                    <th><span>Code</span></th>
                    <th><span>Nom de l'Etablissement</span></th>
                    <th><span>Téléphone</span></th>
                    <th><span>Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="etab in etabs" :key="etab.id">
                    <td>{{ etab.zapName }}</td>
                    <td>{{ etab.code }}</td>
                    <td>{{ etab.nomEtab }}</td>
                    <td>{{ etab.telephone }}</td>
                    <td>
                        <a @click="editEtab(etab)">
                            <img src="/images/logos/edit.png" alt="" width="35px" height="35px" />
                        </a>
                        <a @click="confirmDelete(etab.id)">
                            <img src="/images/logos/delete.png" alt="" width="30px" height="30px" />
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="pagination" class="pagination">
            <ul>
                <li v-for="(link, index) in pagination.links" :key="index" :class="{ 'active': link.active }">
                    <button @click="fetchPage(link.url)" v-html="link.label" :disabled="!link.url"></button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2';
export default {
    props: ['id', 'name'],
    data() {
        return {
            form: {
                idZap: this.id,
                code: '',
                etab: '',
                telephone: '0',
            },
            nom: this.name,
            isEditMode: false,
            searchTerm: '',
            etabs: [],
            pagination: {},
            searchTerm: '',
            isSearchMode: false,
        };
    },
    created() {
        this.fetchEtabs();
    },
    methods: {
        fetchEtabs(pageUrl) {
            const url = pageUrl || `/api/etab/${this.id}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    this.etabs = data.etabs.data || [];
                    this.pagination = data.etabs || {};
                })
                .catch(error => console.error('Erreur lors de la récupération des établissements:', error));
        },
        fetchPage(url) {
            if (url) this.fetchEtabs(url);
        },
        submitForm() {
            const url = this.isEditMode ? `/api/update-etab/traitement` : '/api/ajout-etab';
            const method = this.isEditMode ? 'PUT' : 'POST';
            const formData = { ...this.form };
            if (this.isEditMode) {
                formData.id = this.form.id;  // Ajoutez l'ID dans les données si en mode modification
            }
            fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            })
                .then((response) => response.json())
                .then((data) => {
                    this.status = data.success;
                    if (this.status) {
                        Swal.fire({
                            title: "Message",
                            text: this.status,
                            icon: "success",
                            confirmButtonText: "OK",
                        })
                        this.resetForm();
                        this.fetchEtabs();
                    }
                })
                .catch(error => console.error('Erreur lors de la soumission du formulaire:', error));
        },
        resetForm() {
            this.form = { idZap: this.id, code: '', etab: '', telephone: '0' }; // Utilisez this.id pour initialiser idZap
            this.isEditMode = false;
        },
        editEtab(etab) {
            this.form.id = etab.id || '';
            this.form.idZap = this.id; // Réinitialise idZap avec l'ID du ZAP transmis
            this.form.code = etab.code || '';
            this.form.etab = etab.nomEtab || '';
            this.form.telephone = etab.telephone || '';

            this.isEditMode = true;
        },
        cancelEdit() {
            this.resetForm();
        },
        confirmDelete(etabId) {
            Swal.fire({
                title: "Êtes-vous sûr? Ceci est associé à la table distribution!",
                text: "Voulez-vous vraiment supprimer cet Etablissement?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/api/delete-etab/${etabId}`, { method: 'DELETE' })
                        .then(() => {
                            this.etabs = this.etabs.filter(etab => etab.id !== etabId);
                            Swal.fire("Supprimé!", "L'Etablissement a été supprimé.", "success");
                            this.fetchEtabs(); // Recharge la liste des stocks
                        })
                        .catch(error => {
                            console.error("Erreur lors de la suppression de l'établissement:", error);
                            Swal.fire("Erreur", "Impossible de supprimer l'établissement.", "error");
                        });
                }
            });
        },

        searchEtab() {
            const searchUrl = `/api/recherche-etab?lettres=${this.searchTerm}`;
            fetch(searchUrl)
                .then(response => response.json())
                .then(data => {
                    this.etabs = data.etabs.data || [];
                    this.pagination = data.etabs || {};
                    this.isSearchMode = true; // Active le mode recherche
                })
                .catch(error => console.error("Erreur lors de la recherche:", error));
        },
        showAllEtabs() {
            this.searchTerm = '';
            this.fetchEtabs();
            this.isSearchMode = false;
        },
    },
};
</script>
