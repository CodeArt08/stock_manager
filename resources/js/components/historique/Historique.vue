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
      <form @submit.prevent="showDistributionsBetweenDates" class="hist">
        <button class="btnHist" type="submit">
          <img src="/images/logos/all.png" width="35px" height="35px" />
          <span class="ajt">Distributions entre</span>
        </button>
        <div class="dateStyle">
          <input type="date" v-model="date1" required class="date1" />
          <input type="date" v-model="date2" required class="date1" />
        </div>
      </form>

      <!-- Formulaire pour les ZAP n'ayant pas encore reçu -->
      <form @submit.prevent="showNonDistributedZaps" class="nonDist">
        <button class="btnNonDist" type="submit">
          <span class="ajtna">Etablissements n'ayant pas encore reçus</span>
        </button>
        <div class="style">
          <h4>Date d'arrivée du stock</h4>
          <input type="date" v-model="stockDate" required class="date1" />
        </div>
      </form>

      <!-- Formulaire pour générer un PDF -->
      <form @submit.prevent="generateGrandPdf" class="pdf">
        <button class="btnPdf" type="submit">
          <img src="/images/logos/printing.png" width="30px" height="30px" />
          <span class="ajt">Générer PDF</span>
        </button>
        <div class="style">
          <h4>Date d'arrivée du stock</h4>
          <input type="date" v-model="pdfDate" class="date1" required />
        </div>
      </form>
    </div>



    <!-- Vue dynamique basée sur currentView -->
    <div>
      <div class="flex">
        <h3 v-if="currentView === 'default'">Liste des ZAP</h3>
        <h3 v-else-if="currentView === 'distribution' && date1 && date2">Liste des distributions entre {{ date1 }} et {{
          date2 }}</h3>
        <h3 v-else-if="currentView === 'distribution' && selectedZap">Liste des distributions du ZAP {{ selectedZap.zap
          }}
        </h3>
        <h3 v-else-if="currentView === 'nonDistributed'">Etablissements n'ayant pas encore reçu de stock arrivé le {{
          stockDate }}
        </h3>
        <!-- J'aimerais afficher ceci que lorsqu'on clique sur le bouton showHistorique-->
        <div class="cont">
          <div class="flex1" v-if="showHistoriqueView">
            <div class="unBt">
              <button @click="showAll" v-if="filtered" class="B3">tout afficher</button>
            </div>

            <div class="child2">
              <form @submit.prevent="filterOrGenerate">
                <div>
                  <label for="">date d'arrivée des stocks:</label>
                  <input type="date" v-model="dateArrivee" class="date" required />
                </div>

                <div class="deuxBt">
                  <button @click="action = 'filter'" type="submit" class="B1">filtrer liste</button>
                  <button @click="action = 'pdf'" type="submit" class="B2">Générer PDF</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="flex2" v-if="showNonDistView">
          <div class="unBt2">
            <button @click="showAll2" v-if="filtered2" class="B3">tout afficher</button>
          </div>

          <div class="child2">
            <form @submit.prevent="filter2">
              <div>
                <label for="">Nom du ZAP:</label>
                <select v-model="zapNom" class="select-control" required>
                  <option disabled value="">Sélectionner un ZAP</option>
                  <option v-for="zap in zapPrim" :key="zap.id" :value="zap.zap">
                    {{ zap.zap }}
                  </option>
                </select>
              </div>

              <div class="deuxBt">
                <button type="submit" class="B1">filtrer liste</button>
              </div>
            </form>
          </div>

        </div>

      </div>

      <table v-if="currentView === 'nonDistributed'" class="table">
        <thead>
          <tr>
            <th><span>ZAP</span></th>
            <th><span>Code</span></th>
            <th><span>Nom de l'Etablissement</span></th>
            <th><span>Téléphone</span></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="etab in etabs" :key="etab.id">
            <td>{{ etab.zap }}</td> <!-- Affiche le nom du ZAP -->
            <td>{{ etab.code }}</td>
            <td>{{ etab.nomEtab }}</td>
            <td>{{ etab.telephone }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination pour les établissements dans la vue "nonDistributed" -->
      <div v-if="pagination && currentView === 'nonDistributed'" class="pagination">
        <ul>
          <li v-for="(link, index) in pagination.links" :key="index" :class="{ 'active': link.active }">
            <button @click="fetchPageEtab(link.url)" v-html="link.label" :disabled="!link.url"></button>
          </li>
        </ul>
      </div>

      <!-- Table des ZAPs -->
      <table v-if="currentView === 'default'" class="table">
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
              <button @click="showHistorique(zap)" class="h">historique</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination pour la vue ZAP -->
      <div v-if="pagination && (currentView === 'default')" class="pagination">
        <ul>
          <li v-for="(link, index) in pagination.links" :key="index" :class="{ 'active': link.active }">
            <button @click="fetchPageZap(link.url)" v-html="link.label" :disabled="!link.url"></button>
          </li>
        </ul>
      </div>



      <!-- Table des distributions -->
      <table v-else-if="currentView === 'distribution'" class="table8">
        <thead>
          <tr>
            <th><span>Désignation</span></th>
            <th><span>ZAP</span></th>
            <th><span>Établissement</span></th>
            <th><span>Carton(s)</span></th>
            <th><span>Pièce(s)</span></th>
            <th><span>Total en pièce</span></th>
            <th><span>Date de distribution</span></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dist in dists" :key="dist.id">
            <td>{{ dist.stock }}</td>
            <td>{{ dist.zap }}</td>
            <td>{{ dist.etablissement }}</td>
            <td>{{ dist.nbrCarton }}</td>
            <td>{{ dist.nbrPiece }}</td>
            <td>{{ calculateTotalPiece(dist.nbrCarton, dist.pc, dist.nbrPiece) }}</td>
            <td>{{ dist.dateDist }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination pour la vue Distribution -->
      <div v-if="pagination && currentView === 'distribution'" class="pagination">
        <ul>
          <li v-for="(link, index) in pagination.links" :key="index" :class="{ 'active': link.active }">
            <button @click="fetchPageDist(link.url)" v-html="link.label" :disabled="!link.url"></button>
          </li>
        </ul>
      </div>

    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
  name: "Historique",
  data() {
    return {
      searchTerm: "",
      date1: null,
      date2: null,
      dateArrivee: null,
      stockDate: null,
      pdfDate: null,
      zapNom: null,
      zaps: [],
      zapPrim: [],
      dists: [],
      pagination: {},
      etabs: [],
      currentView: 'default',
      showHistoriqueView: false,
      showNonDistView: false,
      filtered: false,
      filtered2: false,

      selectedZap: null,
      selectedZapName: null,
      selectedZapId: null,


      action: '',
    };
  },
  created() {
    this.fetchZaps();
  },
  methods: {

    searchZap() {
      const searchUrl = `/api/recherche-zap?lettres=${this.searchTerm}`;
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
      this.date1 = null;
      this.date2 = null;
      this.currentView = 'default';
      this.fetchZaps();
      this.showHistoriqueView = false;
      this.showNonDistView = false;
    },


    // CHOIX TABLE ET h3
    showDistributionsBetweenDates() {
      this.currentView = 'distribution';
      this.getDistributionsBetweenDates();
      this.showHistoriqueView = false;
      this.showNonDistView = false;
    },
    showNonDistributedZaps() {
      this.currentView = 'nonDistributed';
      this.getNonDistributedZaps();
      this.fetchZaps2();
      this.showHistoriqueView = false;
      this.showNonDistView = true;
    },


    //PAR DEFAUT
    fetchZaps(url = "/api/historiques") {
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          this.zaps = data.zaps.data || [];
          this.pagination = data.zaps || {};
        });
    },

    fetchZaps2(url = "/api/fetchZap") {
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          if (data.zap) {
            this.zapPrim = data.zap;
          }
        })
        .catch((error) => {
          console.error("Erreur lors de la récupération des ZAPs :", error);
        });
    },



    // PAGINATION

    fetchPage(url, type) {
      if (this.filtered2 && type === 'etab') {
        const queryParams = [];
        if (this.stockDate) queryParams.push(`date=${this.stockDate}`);
        if (this.zapNom) queryParams.push(`zap=${this.zapNom}`);
        const queryString = queryParams.length ? `&${queryParams.join('&')}` : '';
        url = `${url}${queryString}`;
      }
      fetch(url)
        .then(response => response.json())
        .then(data => {
          switch (type) {
            case 'zap':
              this.zaps = data.zaps.data || [];
              this.pagination = data.zaps || {};
              break;
            case 'distribution':
              this.dists = this.formatDistributions(data.dists.data);
              this.pagination = data.dists || {};
              break;
            case 'etab':
              // Ajoutez un formatage des établissements ici
              this.etabs = this.formatEtabs(data.etabs.data || []);
              this.pagination = data.etabs;
              break;
            default:
              console.error(`Type ${type} non pris en charge`);
          }
        })
        .catch(error => console.error("Erreur de pagination :", error));
    },



    fetchPageZap(url) {
      this.fetchPage(url, 'zap');
    },

    fetchPageDist(url) {
      this.fetchPage(url, 'distribution');
    },

    fetchPageEtab(url) {
      this.fetchPage(url, 'etab');
    },

    // UNIVERSEL POUR LES ETABLISSEMENTS
    formatEtabs(etabs) {
      return etabs.map(etab => ({
        id: etab.id,
        zap: etab.zap ? etab.zap.zap : 'N/A', // Vérifiez que le ZAP est inclus dans les données
        code: etab.code,
        nomEtab: etab.nomEtab,
        telephone: etab.telephone,
      }));
    },





    // UNIVERSEL POUR LES DISTRIBUTIONS
    formatDistributions(rawDists) {
      return (rawDists || []).map(dist => ({
        id: dist.id,
        stock: dist.stock ? dist.stock.stock : dist.idStock || 'N/A',
        zap: dist.zap ? dist.zap.zap : dist.idZap || 'N/A',
        etablissement: dist.etablissement ? dist.etablissement.nomEtab : dist.idEtab || 'N/A',
        nbrCarton: dist.nbrCarton,
        nbrPiece: dist.nbrPiece,
        dateDist: dist.dateDist,
        pc: dist.stock ? (dist.stock.pc || 0) : (dist.pc || 0) // Ajout d'une valeur par défaut pour pc
      }));
    },



    // HISTORIQUE, DISTRIBUTION, PDF_kely
    filterOrGenerate() {
      if (this.action === 'filter') {
        this.filterList();
      } else if (this.action === 'pdf') {
        this.generatePdf();
      }
    },
    filterList() {
      console.log(this.dateArrivee);
      const zapId = this.selectedZapId;
      console.log(zapId);
      fetch(`/api/hist-filter?dateArrivee=${this.dateArrivee}&zapId=${zapId}`)
        .then(response => response.json())
        .then(data => {
          console.log("Données reçues :", data.dists);
          if (data.dists) {
            this.dists = (data.dists.data || []).map(dist => ({
              id: dist.id,
              stock: dist.stock ? dist.stock.stock : 'N/A',
              zap: dist.zap ? dist.zap.zap : 'N/A',
              etablissement: dist.etablissement ? dist.etablissement.nomEtab : 'N/A',
              nbrCarton: dist.nbrCarton,
              nbrPiece: dist.nbrPiece,
              dateDist: dist.dateDist,
              pc: dist.stock ? dist.stock.pc : null
            }));
            this.filtered = true;
          } else {
            console.error("Format de réponse incorrect :", data);
          }
        })
        .catch(error => console.error("Erreur lors du filtrage:", error));
    },


    showHistorique(zap) {
      this.currentView = 'distribution';
      this.selectedZap = zap;
      this.selectedZapId = zap.id;
      this.selectedZapName = zap.zap;
      console.log("Selected ZAP ID:", this.selectedZapId); // Ajoutez ce log
      this.showHistoriqueView = true;

      this.fetchDists(`/api/historique-dist/${zap.id}`);
    },


    fetchDists(url = '/api/distributions') {
      fetch(url)
        .then(response => response.json())
        .then(data => {
          this.dists = this.formatDistributions(data.dists.data);
          this.zap = data.zap || '';
          this.pagination = data.dists || {};
        })
        .catch(error => console.error("Erreur lors du chargement des distributions:", error));
    },

    showAll() {
      // Réinitialise les variables de filtrage
      this.dateArrivee = null;
      this.filtered = false;

      // Recharge les distributions en fonction du ZAP sélectionné ou toutes si aucun ZAP n'est sélectionné
      const url = this.selectedZap ? `/api/historique-dist/${this.selectedZap.id}` : '/api/distributions';
      this.fetchDists(url);
    },

    generatePdf() {
      console.log({
        dateArrivee: this.dateArrivee,
        zapId: this.selectedZapId,
      });

      fetch("/api/pdf-zap", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content,
        },
        body: JSON.stringify({ dateArrivee: this.dateArrivee, zapId: this.selectedZapId }),
      })
        .then(response => {
          if (response.ok) {
            return response.blob(); // Si la réponse est OK, récupérer le PDF
          }
          return response.json(); // Sinon, récupérer le message d'erreur
        })
        .then(data => {
          if (data instanceof Blob) {
            const url = URL.createObjectURL(data);
            const link = document.createElement("a");
            link.href = url;
            link.download = `distributions-de-la-ZAP-${this.selectedZapName || "non-specifie"}-pour-les-stocks.pdf`;
            link.click();
            URL.revokeObjectURL(url);
          } else {
            Swal.fire({
              icon: "error",
              title: "Erreur",
              text: data.message || "Erreur lors de la génération du PDF.",
              confirmButtonText: "OK",
            });
          }
        })
        .catch(error => {
          console.error(error);
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de la génération du PDF.",
            confirmButtonText: "OK",
          });
        });
    },




    // ENTRE DEUX DATES
    getDistributionsBetweenDates() {
      const url = `/api/liste-date?date1=${this.date1}&date2=${this.date2}`;
      fetch(url)
        .then(response => response.json())
        .then(data => {
          this.dists = this.formatDistributions(data.dists.data);
          this.date1 = data.date1;
          this.date2 = data.date2;
          this.pagination = data.dists || {};

        })
        .catch(error => console.error("Erreur lors de la recherche:", error));
    },



    //ZAP NON DIST
    getNonDistributedZaps(url = `/api/liste-nonDist?date=${this.stockDate}`) {
      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.etabs = data.etabs.data.map(etab => ({
              id: etab.id,
              zap: etab.zap ? etab.zap.zap : 'N/A',
              code: etab.code,
              nomEtab: etab.nomEtab,
              telephone: etab.telephone,
            }));
            this.pagination = data.etabs;
          } else {
            // Afficher une alerte si la date n'existe pas
            Swal.fire({
              icon: 'info',
              title: 'Aucune correspondance',
              text: data.message, // Message retourné par l'API
              confirmButtonText: 'OK',
            });
          }
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de la récupération des établissements non distribués.',
            confirmButtonText: 'OK',
          });
          console.error('Erreur lors de la récupération des établissements non distribués :', error);
        });
    },


    filter2() {
      if (!this.stockDate) {
        Swal.fire({
          icon: "warning",
          title: "Champ requis",
          text: "Veuillez sélectionner une date.",
          confirmButtonText: "OK",
        });
        return;
      }

      const url = `/api/liste-nonDist2?date=${this.stockDate}${this.zapNom ? `&zap=${this.zapNom}` : ""}`;
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            this.etabs = data.etabs.data.map((etab) => ({
              id: etab.id,
              zap: etab.zap ? etab.zap.zap : "N/A",
              code: etab.code,
              nomEtab: etab.nomEtab,
              telephone: etab.telephone,
            }));
            this.pagination = data.etabs; // Ajout de la pagination
            this.filtered2 = true; // Activer l'état de filtre
          } else {
            Swal.fire({
              icon: "info",
              title: "Aucune correspondance",
              text: data.message,
              confirmButtonText: "OK",
            });
          }
        })
        .catch((error) => {
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors du filtrage.",
            confirmButtonText: "OK",
          });
          console.error("Erreur lors du filtrage :", error);
        });
    },



    showAll2() {
      this.getNonDistributedZaps();
      this.filtered2 = false;
    },







    // GRAND PDF
    generateGrandPdf() {
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




    calculateTotalPiece(nbrCarton, pc, nbrPiece) {
      return (Number(nbrCarton) || 0) * (Number(pc) || 0) + (Number(nbrPiece) || 0);
    },


  },
};
</script>

<style scoped>
/* Styles */
</style>
