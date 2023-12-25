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
                            var triangleSize = 20; // Adjust the size of the triangle as needed
                            var isTurtleVisible = true;  // Variable pour suivre la visibilité de la tortue
                            var previousX = 0;
                            var previousY = 0;
                            var previousPositions = [];

                            function updateCanvas(data) {
                              
                            if (isTurtleVisible) {
                                drawTurtle(ctx, data.x, data.y, data.angle, data.isDrawing);
                            } else {
                                // Si la tortue n'est pas visible, effacez simplement le canvas
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                            }

                            if (data.command === 'CT') {
                                // Cacher la tortue
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                                hideTurtle();
                            } else if (data.command === 'MC') {
                                // Montrer la tortue
                                showTurtle();
                            }

                            console.log('Données reçues du serveur :', data);
                        }

                            function hideTurtle() {
                                // Logique pour cacher la tortue
                                isTurtleVisible = false;
                                //ctx.clearRect(0, 0, canvas.width, canvas.height);
                                clearCanvas();

                                updateCanvas();  // Appel sans argument, car vous n'avez pas de nouvelles données
                                previousX = 0;
                                previousY = 0;
                            }

                            function showTurtle() {
                                // Logique pour montrer la tortue
                                isTurtleVisible = true;
                                // Appel sans argument, car vous n'avez pas de nouvelles données
                                updateCanvas();
                            }
                          
                            function drawTurtle(ctx, centerX, centerY, angle, isDrawing) {
                                ctx.clearRect(0, 0, canvas.width, canvas.height);

                            if (!isTurtleVisible) {
                                return;  // Quitter la fonction si la tortue n'est pas visible
                            }

                            previousPositions.push({ x: centerX, y: centerY });

                            ctx.beginPath();
                            ctx.moveTo(previousX, previousY);
                            ctx.lineTo(centerX, centerY);
                            ctx.stroke();

                            // ... (le reste de votre code)

                            // Mettre à jour les coordonnées précédentes
                            previousX = centerX;
                            previousY = centerY;

                            if (isDrawing) {
                                // Dessiner la trace
                                ctx.beginPath();
                                ctx.moveTo(previousX, previousY);
                                ctx.lineTo(centerX, centerY);
                                ctx.stroke();
                            }

                            ctx.save();
                            ctx.translate(centerX, centerY);
                            ctx.rotate(angle * Math.PI / 180);

                            ctx.beginPath();
                            ctx.moveTo(0, -triangleSize / 2);
                            ctx.lineTo(triangleSize / 2, triangleSize / 2);
                            ctx.lineTo(-triangleSize / 2, triangleSize / 2);
                            ctx.closePath();

                            if (isDrawing) {
                                // Changer la couleur du trait selon les besoins
                                ctx.strokeStyle = "black";
                                ctx.stroke();
                            } else {
                                // Changer la couleur de remplissage selon les besoins
                                ctx.fillStyle = "green";
                                ctx.fill();
                            }

                            ctx.restore();
                        }
                        function clearCanvas() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            // Redessiner les anciennes positions sans les lignes de traçage
                            for (var i = 0; i < previousPositions.length - 1; i++) {
                                ctx.beginPath();
                                ctx.moveTo(previousPositions[i].x, previousPositions[i].y);
                                ctx.lineTo(previousPositions[i + 1].x, previousPositions[i + 1].y);
                                ctx.stroke();
                            }
                        }
                                            
                            document.getElementById('fileInput').addEventListener('change', function () {
                                var fileList = this.files;
                                var fileNames = Array.from(fileList).map(file => file.name);
                                console.log('Fichiers sélectionnés :', fileNames);
                            });
                                function clearConsole() {
                                    var command = "VT"; // Commande pour effacer la console

                                    // Envoyez la commande au backend via Ajax
                                    fetch('/dashboard', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({ command: command })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        // Traitez la réponse du serveur si nécessaire
                                        console.log('Console effacée avec succès');
                                    })
                                    .catch(error => {
                                        console.error('Erreur lors de l\'effacement de la console :', error);
                                    });
                                }
                                var command = "FCC #RRGGBB"; // Remplacez #RRGGBB par la couleur RGB souhaitée
                                var command = "FCAP 45"; // Remplacez 45 par l'angle souhaité
                                var command = "FPOS 100 50"; // Remplacez 100 et 50 par les coordonnées souhaitées
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

