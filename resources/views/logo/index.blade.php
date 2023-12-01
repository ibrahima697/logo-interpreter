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
                        <script>
                            // Récupérer le contexte du canvas
                            var canvas = document.getElementById('logoCanvas');
                            var ctx = canvas.getContext('2d');
                       
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
                        
                            function updateCanvas(data) {
                                // Effacez le contenu actuel du canvas
                                ctx.clearRect(10, 10, canvas.width, canvas.height);
                        
                                // Exemple de dessin basique (la logique réelle dépendra de la commande exécutée)
                                drawTurtle(data.x, data.y);


                                console.log('Données reçues du serveur :',data);
                            }
                            function drawTurtle(x, y) {
                            // Dessinez la tortue en fonction des coordonnées x, y
                            ctx.beginPath();
                            ctx.arc(x, y, 10, 0, 2 * Math.PI);
                            //ctx.fillStyle = 'green';
                            ctx.fill();
                            ctx.stroke();
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

