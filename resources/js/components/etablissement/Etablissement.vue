<template>
  <div>
    <div class="rechercheZap">
      <form @submit.prevent="searchZap" class="formsearch" role="search">
        <input v-model="searchTerm" type="text" placeholder="recherche" class="inputsearch" />
        <button class="btnsearch" type="submit">
          <img src="/images/logos/magnifying-glass.png" alt="" width="20px" height="20px" />
        </button>
      </form>
      <button v-if="isSearchMode" @click="showAllZaps" class="affiche2">Afficher tout</button>
    </div>

    <div class="parent1k">
      <div class="totalZap">
        <img src="/images/logos/all.png" width="35px" height="35px" />
        <span class="ajt">Total ZAP: {{ totalZaps }}</span>
      </div>
      <div class="total">
        <img src="/images/logos/all.png" width="35px" height="35px" />
        <span class="ajt">Total Etablissement: {{ totalEtabs }}</span>
      </div>
    </div>

    <!-- Liste des ZAPs -->
    <div class="badBoy">
      <h3>Liste des ZAP</h3>
      <table class="table">
        <thead>
          <tr>
            <th><span>numZAP</span></th>
            <th><span>DREN</span></th>
            <th><span>CISCO</span></th>
            <th><span>ZAP</span></th>
            <th><span>Nombre d'Etablissement</span></th>
            <th><span>Actions</span></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="zap in zaps" :key="zap.id">
            <td>{{ zap.codeZap }}</td>
            <td>{{ zap.dren }}</td>
            <td>{{ zap.cisco }}</td>
            <td>{{ zap.zap }}</td>
            <td>{{ zap.nbrEtab }}</td>
            <td>
              <router-link :to="{ name: 'listeEtab', params: { id: zap.id, name: zap.zap } }" class="h">Etab</router-link>

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
  </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {

  data() {
    return {
      searchQuery: "",
      zaps: [],
      pagination: {},
      searchTerm: '', 
      totalZaps: null,
      totalEtabs: null, 
      isSearchMode: false,
    };
  },
  created() {
    this.fetchZaps();
  },
  methods: {
    fetchZaps(pageUrl = "/api/etablissements") {
      fetch(pageUrl)
        .then((response) => response.json())
        .then((data) => {
          this.zaps = data.zaps.data || [];
          this.totalZaps = data.totalZaps || 0;
          this.totalEtabs = data.totalEtabs || 0;
          this.pagination = data.zaps || {};
        })
                .catch(error => console.error('Erreur lors de la récupération des établissements:', error));
        },
        fetchPage(url) {
            if (url) this.fetchZaps(url);
        },
        searchZap() {
        const searchUrl = `/api/recherche-zapPrim?lettres=${this.searchTerm}`;
        fetch(searchUrl)
          .then(response => response.json())
          .then(data => {
            this.zaps = data.zaps.data || [];
          this.pagination = data.zaps || {};
            this.isSearchMode = true; // Active le mode recherche
          })
          .catch(error => console.error("Erreur lors de la recherche:", error));
      },
      showAllZaps() {
        this.searchTerm = ''; 
        this.fetchZaps(); 
        this.isSearchMode = false; 
      },
  },

};
</script>

<style scoped>
/* Styles spécifiques au composant */
</style>
