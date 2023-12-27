<body class="antialiased">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div class="mt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white dark:bg-white-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <!-- Zone de dessin -->
                            <canvas id="logoCanvas" width="900" height="500" style="border:2px solid #070707;"> 
                             
                            </canvas>
                        </div>
                        
                        <div >
                            <!-- Formulaire pour le bouton "Exécuter" -->
                            <form  method="post" action="{{url('/dashboard')}}" class="flex space-x- items-stretch" id="executeLogo">
                                @csrf <!-- Ajoutez le jeton CSRF -->
                                <textarea class="inline-flex self-start"  style="border:2px solid #070707;"  name="commandInput" id="commandInput" placeholder="Saisissez vos commandes Logo ici..."></textarea>
                                
                                <input type="file" name="fileInput" id="fileInput" multiple />

                                <button class="bg-green-900 hover:bg-green-700 text-white font-bold py- px-6 border border-green-700 rounded"
                                    type="button" onclick="submitForm();" id="executeLogo">Exécuter</button>

                            </form>
                        </div>
                        
                        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
                            @include('chat.index')
                        </div>
                        <script >
                            // Récupérer le contexte du canvas
                            
                       
                        function submitForm() {
                                var command = document.getElementById('commandInput').value;
                                console.log('Commande envoyée :', command);

                                // Envoyez la commande au backend via Ajax
                                fetch('/dashboard', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ command: command })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Erreur HTTP, statut : ' + response.status);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    // Mettez à jour le canvas avec les données reçues
                                    console.log('Données reçues du serveur :', data);

                                    updateCanvas(data);
                                })
                                .catch(error => {
                                    console.error('Erreur lors de l\'envoi de la commande :', error);
                                });
                                var command = commandInput.value.toUpperCase(); // Convertir en majuscules pour la correspondance insensible à la casse

                                // Exemple de traitement de commandes spécifiques
                                if (command === 'CT') {
                                    // Logique pour cacher la tortue
                                    clearCanvas();
                                    hideTurtle();
                                    console.log('Commande reçue : Cacher la tortue');
                                    // Appeler la fonction pour cacher la tortue ici
                                } else if (command === 'MC') {
                                    // Logique pour montrer la tortue
                                    showTurtle();

                                    console.log('Commande reçue : Montrer la tortue');
                                    // Appeler la fonction pour montrer la tortue ici
                                } else if (command === 'VE') {
                                    // Logique pour effacer le canvas
                                  clearCanvas();

                                    console.log('Commande reçue : Replacer la tortue au centre');
                                    // Appeler la fonction pour montrer la tortue ici
                                } else if (command === 'NETTOIE'){
                                    previousPositions = [];
                                    console.log('Commande reçue : effacer les traits');
                                } else if (data.command === 'BC') {
                                    // Baisser le crayon
                                    penDown();
                                } else if (data.command === 'LC') {
                                    // Lever le crayon
                                    penUp();
                                } else if (data.command === 'FCB') {
                                    // Change the background color of the canvas
                                    changeCanvasBackgroundColor(data.backgroundColor);
                                }
                                 else {
                                    // Commande non reconnue
                                    console.log('Commande non reconnue : ' + command);
                                }

                                // Effacer le champ de saisie après le traitement de la commande
                                commandInput.value = '';

                                    }
                                document.addEventListener('DOMContentLoaded', function() {
                                    var canvas = document.getElementById('logoCanvas');
                                    var ctx = canvas.getContext('2d');
                                    
                                    // Utilisation de la fonction drawTurtle
                                });
                                var ctx = document.getElementById("logoCanvas").getContext("2d");
                                var canvas = document.getElementById('logoCanvas');
                                var centerX = canvas.width / 2;
                                var centerY = canvas.height / 2; 
                                var triangleSize = 10; // Adjust the size of the triangle as needed
                                var isTurtleVisible = true;  // Variable pour suivre la visibilité de la tortue
                                var previousX = 0;
                                var previousY = 0;
                                var previousPositions = [];
                                let isPenDown = true; // Par défaut, le crayon est baissé

                            function updateCanvas(data) {
                                console.log('Données reçues du serveur :', data);

                            if (isTurtleVisible) {
                                drawTurtle(ctx, data.x, data.y, data.angle, data.isDrawing, data.penColor);
                            } else {
                                // Si la tortue n'est pas visible, effacez simplement le canvas
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                            }
                            if (data.isDrawing) {
                                // Dessiner une ligne de la dernière position à la nouvelle position
                                ctx.beginPath();
                                ctx.moveTo(previousX, previousY);
                                ctx.lineTo(data.x, data.y);
                                ctx.stroke();
                            }
                                previousX = data.x;
                                previousY = data.y;

                            console.log('Données reçues du serveur :', data);
                        }

                            function hideTurtle() {
                            console.log('Cacher la tortue');
                            isTurtleVisible = false;
                            updateCanvas();
                            previousX = 0;
                            previousY = 0;
                        }

                        function showTurtle() {
                            console.log('Montrer la tortue');
                            isTurtleVisible = true;
                            updateCanvas();
                        }
                        function penDown() {
                            isPenDown = true;
                            updateCanvas();

                        }

                        function penUp() {
                            isPenDown = false;
                            updateCanvas();

                        }
                        function changeCanvasBackgroundColor(color) {
                        // Set the background color of the canvas
                        canvas.style.backgroundColor = color;
                        updateCanvas();

                    }

                            function drawTurtle(ctx, centerX, centerY, angle, isDrawing,data, penColor) {
                            // Ajouter les coordonnées actuelles au tableau des positions précédentes
                            previousPositions.push({ x: centerX, y: centerY });

                            // Effacer le canvas
                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                            if (!isTurtleVisible) {
                                return;  // Quitter la fonction si la tortue n'est pas visible
                            }

                            // Le reste de votre code existant pour les autres commandes
                            if (isPenDown && previousPositions.length > 1) {
                                // Dessiner toutes les traces précédentes
                                ctx.beginPath();
                                ctx.moveTo(previousPositions[0].x, previousPositions[0].y);
                                for (let i = 1; i < previousPositions.length; i++) {
                                    ctx.lineTo(previousPositions[i].x, previousPositions[i].y);
                                }
                                ctx.strokeStyle = penColor;
                                ctx.stroke();
                            }

                            // Dessiner la tortue actuelle
                            if (isPenDown) {
                                ctx.save();
                                ctx.translate(centerX, centerY);
                                ctx.rotate(angle * Math.PI / 180);
                           

                                // Dessin de la carapace de la tortue
                                ctx.beginPath();
                                ctx.arc(0, 0, triangleSize, 0, 2 * Math.PI, false);
                                ctx.fillStyle = "green";
                                ctx.fill();
                                ctx.lineWidth = 2;
                                ctx.strokeStyle = "black";
                                ctx.stroke();

                                // Dessin des pattes avant
                                drawLeg(ctx, -triangleSize / 2, triangleSize / 2);

                                // Dessin des pattes arrière
                                drawLeg(ctx, triangleSize / 2, triangleSize / 2);

                                // Dessin de la tête
                                ctx.beginPath();
                                ctx.arc(triangleSize * 1.2, 0, triangleSize / 2, 0, 2 * Math.PI, false);
                                ctx.fillStyle = "green";
                                ctx.fill();
                                ctx.stroke();

                                ctx.restore();
                            }
                        
                    }
                        function drawLeg(ctx, x, y) {
                            ctx.beginPath();
                            ctx.moveTo(x, y);
                            ctx.lineTo(x - triangleSize / 4, y + triangleSize / 4);
                            ctx.lineWidth = 2;
                            ctx.stroke();
                        }
                        function clearCanvas() {
                            console.log('Effacer le canvas');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        previousPositions = [];  // Remise à zéro des positions précédentes
                         updateCanvas(); 
                    }
                            document.getElementById('fileInput').addEventListener('change', function () {
                                var fileList = this.files;
                                var fileNames = Array.from(fileList).map(file => file.name);
                                console.log('Fichiers sélectionnés :', fileNames);
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

