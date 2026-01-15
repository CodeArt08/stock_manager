<template>
  <div :class="{ 'blur-background': isModalVisible || isModalVisible2 }">
    <h1>Gestion de stock de CISCO Brickaville</h1>

    <div class="parent1">
      <button @click="openModal" class="ajoutk">
        <img src="/images/logos/plusvrai.png" width="25px" height="25px" />
        <span class="ajtk">AJOUT STOCK</span>
      </button>


      <div class="total">
        <img src="/images/logos/all.png" width="35px" height="35px" />
        <span class="ajt">Total Stock: {{ totalCartons }}C + {{ totalPieces }}P</span>
      </div>

      <div class="total">
        <img src="/images/logos/total-fat.png" width="35px" height="35px" />
        <span class="ajt">Total distribuée: {{ totalCartonsDist }}C + {{ totalPiecesDist }}P</span>
      </div>
    </div>

    <div>
      <div class="fin">
        <h3>Liste des stocks</h3>
        <form @submit.prevent="searchStock" class="formsearch2">
          <input type="date" v-model="searchDate" placeholder="recherche" class="inputsearch" />
          <button class="btnsearch" type="submit">
            <img src="/images/logos/magnifying-glass.png" alt="" width="20px" height="20px" />
          </button>
        </form>

        <button v-if="isSearchMode" @click="showAllStocks" class="affiche">Afficher tout</button>
      </div>


      <table class="table">
        <thead>
          <tr>
            <th><span>Designation</span></th>
            <th><span>nombre de carton</span></th>
            <th><span>nombre de pièce</span></th>
            <th><span>pièce/carton</span></th>
            <th><span>date d'arrivée</span></th>
            <th><span>Actions</span></th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="stock in stocks" :key="stock.id" class="tr">
            <td>{{ stock.stock }}</td>
            <td>{{ stock.nbrCarton }}</td>
            <td>{{ stock.nbrPiece }}</td>
            <td>{{ stock.pc }}</td>
            <td>{{ stock.dateArrivee }}</td>
            <td>
              <div class="action">
                <a @click="openModal2(stock.id)">
                  <img src="/images/logos/edit.png" alt="" width="30px" height="30px" />
                </a>

                <a class="delete" @click="confirmDelete(stock.id)">
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
    <ajout-stock @close="closeModal" @userAdded="onUserAdded"/>
  </div>

  <div v-if="isModalVisible2" class="modal-overlay">
    <update-stock :id="selectedStockId" @close="closeModal2" @userAdded="onUserAdded"/>
  </div>


</template>

<script>
import Swal from 'sweetalert2';
import AjoutStock from './ajoutStock.vue';
import UpdateStock from './updateStock.vue';
export default {
  components: {
    AjoutStock,
    UpdateStock
  },
  data() {
    return {
      stocks: [],
      totalCartons: 0,
      totalPieces: 0,
      totalCartonsDist: 0,
      totalPiecesDist: 0,
      pagination: {},
      searchDate: '',
      isSearchMode: false,

      isModalVisible: false,

      selectedStockId: null,
      isModalVisible2: false,
    };
  },
  created() {
    this.fetchStocks();
  },
  methods: {

    onUserAdded() {
            this.fetchStocks();
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

    fetchStocks(pageUrl = '/api/stocks') {
      fetch(pageUrl)
        .then(response => response.json())
        .then(data => {
          console.log(data); // Vérifiez la structure de `data` ici
          this.stocks = data.stocks.data || [];
          this.totalCartons = data.totalCartons || 0;
          this.totalPieces = data.totalPieces || 0;
          this.totalCartonsDist = data.totalCartonsDist || 0;
          this.totalPiecesDist = data.totalPiecesDist || 0;
          this.pagination = data.stocks || {};
          this.isSearchMode = false;
        })
        .catch(error => console.error('Erreur lors de la récupération des stocks:', error));
    },
    fetchPage(url) {
      if (url) {
        this.fetchStocks(url);
      }
    },
    searchStock() {
      const searchUrl = `/api/recherche-stock?lettres=${this.searchDate}`;
      fetch(searchUrl)
        .then(response => response.json())
        .then(data => {
          this.stocks = data.stocks.data || [];
          this.totalCartons = data.totalCartons || 0;
          this.totalPieces = data.totalPieces || 0;
          this.totalCartonsDist = data.totalCartonsDist || 0;
          this.totalPiecesDist = data.totalPiecesDist || 0;
          this.pagination = data.stocks || {};
          this.isSearchMode = true; // Active le mode recherche
        })
        .catch(error => console.error("Erreur lors de la recherche:", error));
    },
    showAllStocks() {
      this.searchDate = '';
      this.fetchStocks();
      this.isSearchMode = false;
    },

    confirmDelete(stockId) {
      Swal.fire({
        title: "Êtes-vous sûr? Ceci est associé à la table distribution!",
        text: "Voulez-vous vraiment supprimer ce stock?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler',
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`/api/delete-stock/${stockId}`, { method: 'DELETE' })
            .then(() => {
              this.stocks = this.stocks.filter(stock => stock.id !== stockId);
              Swal.fire("Supprimé!", "Le stock a été supprimé.", "success");
              this.fetchStocks(); // Recharge la liste des stocks
            })
            .catch(error => {
              console.error("Erreur lors de la suppression du stock:", error);
              Swal.fire("Erreur", "Impossible de supprimer le stock.", "error");
            });
        }
      });
    },
  },
};
</script>
