<template>
  <div :class="{ 'blur-background': isModalVisible || isModalVisible2 }">
    <div class="rechercheZap">
      <form @submit.prevent="searchZap" class="formsearch" role="search">
        <input v-model="searchTerm" type="text" placeholder="recherche" class="inputsearch" />
        <button class="btnsearch" type="submit">
          <img src="/images/logos/magnifying-glass.png" alt="" width="20px" height="20px" />
        </button>
      </form>
      <button v-if="isSearchMode" @click="showAllZaps" class="affiche2">Afficher tout</button>
    </div>

    <!-- Informations et Actions -->
    <div class="parent1">
      <button @click="openModal" class="ajoutk">
        <img src="/images/logos/plusvrai.png" width="25px" height="25px" />
        <span class="ajtk">AJOUT ZAP</span>
      </button>
      <div class="totalZap">
        <img src="/images/logos/all.png" width="35px" height="35px" />
        <span class="ajt">Total ZAP: {{ totalZap }}</span>
      </div>
      <div class="total">
        <img src="/images/logos/all.png" width="35px" height="35px" />
        <span class="ajt">Total Stock: {{ totalCartons }}C + {{ totalPieces }}P</span>
      </div>
      <div class="total">
        <img src="/images/logos/total-fat.png" width="35px" height="35px" />
        <span class="ajt">Total distribuée: {{ totalCartonsDist }}C + {{ totalPiecesDist }}P</span>
      </div>
    </div>

    <!-- Liste des ZAP -->
    <div>
      <h3>Liste des ZAP</h3>
      <table class="table">
        <thead class="head">
          <tr>
            <th><span>code ZAP</span></th>
            <th><span>DREN</span></th>
            <th><span>CISCO</span></th>
            <th><span>ZAP</span></th>
            <th><span>Nombre d'Etablissement</span></th>
            <th><span>Actions</span></th>
          </tr>
        </thead>
        <tbody class="body">
          <tr v-for="zap in zaps" :key="zap.id" class="tr">
            <td>{{ zap.codeZap }}</td>
            <td>{{ zap.dren }}</td>
            <td>{{ zap.cisco }}</td>
            <td>{{ zap.zap }}</td>
            <td>{{ zap.nbrEtab }}</td>
            <td>
              <div class="action">
                <a @click="openModal2(zap.id)">
                  <img src="/images/logos/edit.png" alt="" width="30px" height="30px" />
                </a>
                <a class="delete" @click="confirmDelete(zap.id)">
                  <img src="/images/logos/delete.png" alt="" width="30px" height="30px" />
                </a>
              </div>

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
  <div v-if="isModalVisible" class="modal-overlay">
    <ajout-zap @close="closeModal" @userAdded="onUserAdded" />
  </div>

  <div v-if="isModalVisible2" class="modal-overlay">
    <update-zap :id="selectedStockId" @close="closeModal2" @userAdded="onUserAdded" />
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import AjoutZap from './ajoutZap.vue';
import UpdateZap from './updateZap.vue';
export default {
  components: {
    AjoutZap, UpdateZap
  },
  data() {
    return {
      searchTerm: "",
      totalZap: 0,
      totalCartons: 0,
      totalPieces: 0,
      totalCartonsDist: 0,
      totalPiecesDist: 0,
      zaps: [],
      pagination: { links: [] },
      searchTerm: '',
      isSearchMode: false,

      isModalVisible: false,

      selectedStockId: null,
      isModalVisible2: false,
    };
  },
  created() {
    this.fetchZaps();
  },
  methods: {
    onUserAdded() {
      this.fetchZaps();
    },
    openModal() {
      this.isModalVisible = true;
    },
    closeModal() {
      this.isModalVisible = false;
    },

    openModal2(stockId) {
      this.selectedStockId = stockId;
      this.isModalVisible2 = true;
    },
    closeModal2() {
      this.isModalVisible2 = false;
      this.selectedStockId = null;
    },

    confirmDelete(zapId) {
      Swal.fire({
        title: "Êtes-vous sûr? Ceci est associé à des Etabliisements voire même dans une distribution!",
        text: "Voulez-vous vraiment supprimer ce ZAP?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: "Annuler",
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`/api/delete-zap/${zapId}`, { method: 'DELETE' })
            .then(() => {
              this.zaps = this.zaps.filter(zap => zap.id !== zapId);
              Swal.fire("Supprimé!", "Le ZAP a été supprimé.", "success");
              this.fetchZaps(); // Recharge la liste des stocks
            })
            .catch(error => {
              console.error("Erreur lors de la suppression du ZAP:", error);
              Swal.fire("Erreur", "Impossible de supprimer le ZAP.", "error");
            });
        }
      });
    },
    fetchZaps(pageUrl = "/api/zaps") {
      fetch(pageUrl)
        .then((response) => response.json())
        .then((data) => {
          this.zaps = data.zaps.data || [];
          this.totalZap = data.total || 0;
          this.totalCartons = data.totalCartons || 0;
          this.totalPieces = data.totalPieces || 0;
          this.totalCartonsDist = data.totalCartonsDist || 0;
          this.totalPiecesDist = data.totalPiecesDist || 0;
          this.pagination = data.zaps || {};
        })
        .catch(error => console.error('Erreur lors de la récupération des stocks:', error));
    },

    fetchPage(url) {
      if (url) {
        this.fetchZaps(url);
      }
    },
    searchZap() {
      const searchUrl = `/api/recherche-zap?lettres=${this.searchTerm}`;
      fetch(searchUrl)
        .then(response => response.json())
        .then(data => {
          this.zaps = data.zaps.data || [];
          this.totalZap = data.total || 0;
          this.totalCartons = data.totalCartons || 0;
          this.totalPieces = data.totalPieces || 0;
          this.totalCartonsDist = data.totalCartonsDist || 0;
          this.totalPiecesDist = data.totalPiecesDist || 0;
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
