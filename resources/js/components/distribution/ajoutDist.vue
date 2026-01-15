<template>
  <div class="add-distribution">


    <!-- Alerts -->
    <div v-if="errorMessage" class="alert alert-danger">
      {{ errorMessage }}
    </div>
    <div v-if="statusMessage" class="alert alert-success">
      {{ statusMessage }}
    </div>

    <div class="Zk">

      <div class="Style1">

        <!-- Calculation Form -->
        <div class="goat">
          <form @submit.prevent="convertirPieces" class="formulaireJr">
            <div class="formk">
              <label class="form-label">Nombre de pièces:</label>
              <input type="number" class="form-controlJr" v-model.number="nbrP" required min="0" />
            </div>
            <div class="formk">
              <label class="form-label">Nombre de pièces/carton:</label>
              <input type="number" class="form-controlJr" v-model.number="pc" required min="1" />
            </div>
            <button type="submit" class="btnCalcul"> = </button>
            <div class="formk">
              <label class="form-label">Carton(s):</label>
              <input type="number" class="form-controlJr" :value="cartons" readonly />
            </div>
            <div class="formk">
              <label class="form-label">Pièce(s):</label>
              <input type="number" class="form-controlJr" :value="piecesRestantes" readonly />
            </div>
          </form>
        </div>
        <!-- Image Section -->
        <div>
          <img src="/images/logos/drone-delivery.png" alt="Drone Delivery" width="280px" height="280px"
            class="delivery" />
        </div>

      </div>




      <!-- Right Section: Add Distribution Form -->
      <div class="Z2k">
        <form @submit.prevent="submitForm" class="formulairek">
          <div class="form">
            <div>
              <h2 class="loginTitre"> Formulaire</h2>
            </div>
            <label class="form-label">Designation:</label>
            <select v-model="distribution.stock" class="select-control" required>
              <option disabled value="">Sélectionner un stock</option>
              <option v-for="stock in stocks" :key="stock.id" :value="stock.stock">
                {{ stock.stock }}
              </option>
            </select>
          </div>

          <div class="form">
            <label class="form-label">ZAP:</label>
            <select v-model="distribution.zap" class="select-control" required>
              <option disabled value="">Sélectionner un ZAP</option>
              <option v-for="zap in zaps" :key="zap.id" :value="zap.zap">
                {{ zap.zap }}
              </option>
            </select>
          </div>

          <div class="form">
            <label class="form-label">Code de l'Etablissement:</label>
            <input type="text" class="form-control" v-model="distribution.code" required />
          </div>

          <div class="form">
            <label class="form-label">Nombre de carton:</label>
            <input type="number" class="form-control" v-model.number="distribution.nbrCarton" required min="0" />
          </div>

          <div class="form">
            <label class="form-label">Nombre de pièce:</label>
            <input type="number" class="form-control" v-model.number="distribution.nbrPiece" required min="0" />
          </div>

          <div class="form">
            <label class="form-label">Date de distribution:</label>
            <input type="date" class="form-control" v-model="distribution.dateDist" required />
          </div>

          <div class="bestOf">
            <button class="btnAjout">AJOUTER</button>
          </div>
          <div class="end2" @click="annuler">
            <span>Annuler</span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>


<script>
import Swal from "sweetalert2";

export default {
  name: "AjoutDist",
  data() {
    return {
      // Calculation Form Data
      nbrP: null,
      pc: null,
      cartons: 0,
      piecesRestantes: 0,

      // Distribution Form Data
      distribution: {
        stock: "",
        zap: "",
        code: "",
        nbrCarton: null,
        nbrPiece: null,
        dateDist: "",
      },

      // Lists for Select Inputs
      stocks: [],
      zaps: [],

      // Alerts
      statusMessage: "",
      errorMessage: "",
    };
  },
  created() {
    this.fetchStocksZaps();
  },
  methods: {
    // Fetch Stocks from API
    fetchStocksZaps() {
      fetch("/api/ajout-dist")
        .then((response) => response.json())
        .then((data) => {
          this.stocks = data.stocks || [];
          this.zaps = data.zaps || [];
        })
        .catch((error) => {
          console.error("Erreur lors de la récupération des stocks:", error);
          this.errorMessage = "Impossible de récupérer les stocks.";
        });
    },


    // Handle Calculation Form Submission
    convertirPieces() {
      // Validation
      if (this.nbrP === null || this.pc === null) {
        Swal.fire("Erreur", "Veuillez entrer les nombres de pièces et de pièces/carton.", "error");
        return;
      }

      if (this.nbrP < 0 || this.pc <= 0) {
        Swal.fire("Erreur", "Les valeurs doivent être positives et pièces/carton > 0.", "error");
        return;
      }

      // Calculations
      this.cartons = Math.floor(this.nbrP / this.pc);
      this.piecesRestantes = this.nbrP % this.pc;
    },
    annuler() {
      this.$emit('close');
    },

    submitForm() {
      const formData = { ...this.distribution };

      fetch("/api/ajout-dist/traitement", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(formData),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            this.statusMessage = data.success;
            this.errorMessage = "";
            Swal.fire("Succès", data.success, "success");

            this.$emit("userAdded");
            // Émet un événement pour fermer la modale
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
        .catch((error) => {
          this.$emit('close');
          console.error("Erreur lors de la soumission du formulaire:", error);
          this.errorMessage = "Une erreur est survenue lors de la soumission.";
          Swal.fire("Erreur", "Une erreur est survenue lors de la soumission.", "error");
        });
    },


    // Reset Distribution Form
    resetForm() {
      this.distribution = {
        stock: "",
        zap: "",
        code: "",
        nbrCarton: null,
        nbrPiece: null,
        dateDist: "",
      };
      this.cartons = 0;
      this.piecesRestantes = 0;
    },
  },
};
</script>