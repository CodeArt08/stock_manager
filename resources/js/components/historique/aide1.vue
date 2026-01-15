<template>
    <div>
      <div class="bref">
        <form @submit.prevent="searchZap" class="formsearch" role="search">
          <input v-model="searchTerm" type="text" placeholder="recherche" class="inputsearch" />
          <button class="btnsearch" type="submit">
            <img src="/images/logos/magnifying-glass.png" alt="" width="20px" height="20px" />
          </button>
        </form>
        <button @click="showAllZaps" class="affiche3">ZAP</button>
      </div>
  
  
      <div class="parent1">
        <!-- Formulaire de distribution entre deux dates -->
        <form @submit.prevent="getDistributionsBetweenDates" class="hist">
          <button class="btnHist" type="submit">
            <img src="/images/logos/all.png" width="35px" height="35px" />
            <span class="ajt">Distributions entre</span>
          </button>
          <div class="dateStyle">
            <input type="date" v-model="date1" class="date1" />
            <input type="date" v-model="date2" class="date1" />
          </div>
        </form>
  
        <!-- Formulaire pour les ZAP n'ayant pas encore reçu -->
        <form @submit.prevent="getNonDistributedZaps" class="nonDist">
          <button class="btnNonDist" type="submit">
            <img src="/images/logos/total-fat.png" width="35px" height="35px" />
            <span class="ajt">ZAP n'ayant pas encore réçu</span>
          </button>
          <div class="style">
            <h4>Date d'arrivée du stock</h4>
            <input type="date" v-model="stockDate" class="date1" />
          </div>
        </form>
  
        <!-- Formulaire pour générer un PDF -->
        <form @submit.prevent="generatePdf" class="pdf">
          <button class="btnPdf" type="submit">
            <img src="/images/logos/folder.png" width="35px" height="35px" />
            <span class="ajt">Générer PDF</span>
          </button>
          <div class="style">
            <h4>Date d'arrivée du stock</h4>
            <input type="date" v-model="pdfDate" class="date1" required />
          </div>
        </form>
      </div>
  
      <!-- Liste des ZAPs -->
      <div>
        <h3>Liste des ZAP </h3>
        <h3 style="display: none">ZAP n'ayant pas encore reçu de stock arrivée le {{ dateArrivee }}</h3>
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
              <td>{{ zap.id }}</td>
              <td>{{ zap.dren }}</td>
              <td>{{ zap.cisco }}</td>
              <td>{{ zap.zap }}</td>
              <td>{{ zap.nbrEtab }}</td>
              <td>
                <router-link :to="{ name: 'histDist', params: { id: zap.id } }" class="h">historique</router-link>
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
    name: "ZapList",
  
    data() {
      return {
        searchQuery: "",
        date1: null,
        date2: null,
        stockDate: null,
        pdfDate: null,
        zaps: [],
        pagination: {},
        searchTerm: '',
  
      };
    },
    created() {
      this.fetchZaps();
    },
    methods: {
      searchZap() {
        const searchUrl = `/api/recherche-zapPrim?lettres=${this.searchTerm}`;
        fetch(searchUrl)
          .then(response => response.json())
          .then(data => {
            this.zaps = data.zaps.data || [];
            this.pagination = data.zaps || {};
  
          })
          .catch(error => console.error("Erreur lors de la recherche:", error));
      },
      showAllZaps() {
        this.searchTerm = '';
        this.fetchZaps();
      },
      getDistributionsBetweenDates() {
        this.fetchZaps(`/api/liste-date?date1=${this.date1}&date2=${this.date2}`);
      },
      getNonDistributedZaps() {
        const url = `/api/liste-nonDist?date=${this.stockDate}`;
        fetch(url)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Si des ZAPs non distribués sont trouvés, les afficher
              this.zaps = data.zapNonDist.data || [];
              this.pagination = data.zapNonDist || {};
            } else {
              // Aucun stock ou tous les ZAPs ont reçu la distribution
              Swal.fire({
                icon: 'info',
                title: 'Information',
                text: data.message,
                confirmButtonText: 'OK'
              });
            }
          })
          .catch(error => {
            console.error("Erreur lors de la récupération des ZAPs non distribués:", error);
            Swal.fire({
              icon: 'error',
              title: 'Erreur',
              text: 'Une erreur est survenue lors de la récupération des données.',
              confirmButtonText: 'OK'
            });
          });
      },
      generatePdf() {
        fetch("/api/generate-stock-pdf", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content,
          },
          body: JSON.stringify({ dateArrivee: this.pdfDate }),
        })
          .then((response) => {
            if (response.ok) {
              return response.blob();
            } else {
              return response.json(); // Retourner la réponse JSON pour récupérer le message d'erreur
            }
          })
          .then((data) => {
            if (data instanceof Blob) {
              const url = URL.createObjectURL(data);
              const link = document.createElement("a");
              link.href = url;
              link.download = "stock.pdf";
              link.click();
              URL.revokeObjectURL(url);
            } else {
              // Afficher un message d'erreur avec SweetAlert si l'API renvoie une erreur
              Swal.fire({
                icon: "error",
                title: "Erreur",
                text: data.message || "Erreur lors de la génération du PDF.",
                confirmButtonText: "OK",
              });
            }
          })
          .catch((error) => {
            console.error(error);
            Swal.fire({
              icon: "error",
              title: "Erreur",
              text: "Une erreur est survenue lors de la génération du PDF.",
              confirmButtonText: "OK",
            });
          });
      },
  
      fetchPage(url) {
        if (url) {
          // Ajoutez la date d'arrivée à l'URL pour la pagination
          const paginatedUrl = `${url}&date=${this.stockDate}`;
          fetch(paginatedUrl)
            .then(response => response.json())
            .then(data => {
              this.zaps = data.zapNonDist.data || [];
              this.pagination = data.zapNonDist || {};
            })
            .catch(error => console.error("Erreur lors de la pagination :", error));
        }
      },
  
  
      fetchZaps(url = "/api/historiques") {
  
        fetch(url)
          .then((response) => response.json())
          .then((data) => {
            this.zaps = data.zaps.data || [];
            this.pagination = data.zaps || {};
          });
      },
  
      fetchPage(url) {
        if (url) {
          this.fetchZaps(url);
        }
      },
    },
  
  };
  </script>
  
  <style scoped>
  /* Styles spécifiques pour le composant */
  </style>
  