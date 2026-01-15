<template>
    <div>
        <div class="bref">
            <form @submit.prevent="searchZap" class="formsearch" role="search">
                <input v-model="searchTerm" type="text" placeholder="recherche" class="inputsearch" />
                <button class="btnsearch" type="submit">
                    <img src="/images/logos/magnifying-glass.png" alt="" width="20px" height="20px" />
                </button>
            </form>
            <router-link to="/liste-historique">
                <button @click="showAllZaps" class="affiche3">ZAP</button>
            </router-link>
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
                    <h4>Date d'arrivée des stocks</h4>
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
                    <h4>Date d'arrivée des stocks</h4>
                    <input type="date" v-model="pdfDate" class="date1" required />
                </div>
            </form>
        </div>

        <!-- Liste des ZAPs -->
        <div>
            <div class="flex">
                <h3>Liste des distributions du ZAP {{ zap }}</h3>
                <div class="flex1">
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

            <table class="table2">
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
                        <td>{{ dist.stock || 'N/A' }}</td> <!-- Nom du stock -->
                        <td>{{ dist.zap || 'N/A' }}</td> <!-- Nom du ZAP -->
                        <td>{{ dist.etablissement || 'N/A' }}</td> <!-- Nom de l'établissement -->
                        <td>{{ dist.nbrCarton }}</td>
                        <td>{{ dist.nbrPiece }}</td>
                        <td>{{ calculateTotalPiece(dist.nbrCarton, dist.pc, dist.nbrPiece) || 'N/A' }}</td>
                        <td>{{ dist.dateDist }}</td>
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
export default {
    props: {
        id: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            dateArrivee: null,
            dists: [],
            zap: "",
            pagination: {},
            filtered: false,
            action: null // Pour savoir si le bouton "filter" ou "pdf" a été cliqué
        };
    },
    methods: {
        filterOrGenerate() {
            if (this.action === 'filter') {
                this.filterList();
            } else if (this.action === 'pdf') {
                this.generatePdf();
            }
        },
        filterList() {
            console.log(this.dateArrivee);
            fetch(`/api/hist-filter?dateArrivee=${this.dateArrivee}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Données reçues :", data.dists); // Vérifiez que les relations sont présentes
                    if (data.dists) {
                        this.dists = (data.dists.data || []).map(dist => ({
                            id: dist.id,
                            stock: dist.stock ? dist.stock.stock : 'N/A', // Nom du stock
                            zap: dist.zap ? dist.zap.zap : 'N/A',         // Nom du ZAP
                            etablissement: dist.etablissement ? dist.etablissement.nomEtab : 'N/A', // Nom de l'établissement
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





        generatePdf() {
            fetch("/api/pdf-zap", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content,
                },
                body: JSON.stringify({ dateArrivee: this.dateArrivee, zapName: this.zap }),
            })
                .then(response => {
                    if (response.ok) return response.blob();
                    throw new Error("Erreur lors de la génération du PDF.");
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement("a");
                    link.href = url;
                    link.download = "distribution_stock.pdf";
                    link.click();
                    URL.revokeObjectURL(url);
                })
                .catch(error => console.error(error));
        },

        showAll() {
            this.fetchDists();
            this.filtered = false;
        },
        fetchDists(url = `/api/historique-dist/${this.id}`) {
            fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    this.dists = (data.dists.data || []).map(dist => ({
                        id: dist.id,
                        stock: dist.idStock || 'N/A',      // Nom du stock
                        zap: dist.idZap || 'N/A',          // Nom du ZAP
                        etablissement: dist.idEtab || 'N/A', // Nom de l'établissement
                        nbrCarton: dist.nbrCarton,
                        nbrPiece: dist.nbrPiece,
                        dateDist: dist.dateDist,
                        pc: dist.pc
                    }));
                    this.zap = data.zap || '';
                    this.pagination = data.dists || {};
                });
        },

        fetchPage(url) {
            if (url) {
                this.fetchDists(url);
            }
        },

        calculateTotalPiece(nbrCarton, pc, nbrPiece) {
            if (pc == null || isNaN(pc)) {
                console.warn("Le champ 'pc' est manquant ou invalide pour certains stocks.");
                return nbrPiece; // Retourne seulement nbrPiece si pc est invalide
            }
            return nbrCarton * pc + nbrPiece;
        }




    },
    mounted() {
        this.fetchDists();
    }
};
</script>