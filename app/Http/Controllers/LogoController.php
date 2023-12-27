<?php

namespace App\Http\Controllers;

use App\Models\LogoCommand;
use Illuminate\Http\Request;
use App\Services\InterpreteurLogo;
use Illuminate\Support\Facades\Storage;


class LogoController extends Controller
{
    protected $currentPosition;
    protected $isVisible = true;
    protected $isHidden = true;
    //protected $isDrawing = true;
    protected $positionX;
    protected $positionY;
    protected $angle;
    protected $color;
    protected $distance;
    protected $canvasWidth;  // Ajustez la valeur en conséquence
    protected $canvasHeight;
    protected $angleInRadians;
    private $isTurtleVisible = true; // La tortue est visible par défaut
    private $penColor = "black"; // Couleur du trait par défaut
    private $previousPositions = []; // Tableau pour stocker les positions précédentes

    protected $session ;

    public function index(Request $request)
    {
     
        try {
            if ($request->isMethod('post')) {
                $command = $request->input('command');
                $currentPosition = $this->getInitialTurtlePosition();
                $interpretationResult = $this->interpretWithMLLibrary($command, $currentPosition);
    
                // Retourner les données interprétées au format JSON
                return response()->json($interpretationResult);

                // Interpréter la commande avec la bibliothèque ML
                $interpretationResult = $this->interpretWithMLLibrary($command);
                InterpreteurLogo::getInstance()->setX();
                sleep(1);
                InterpreteurLogo::getInstance()->getX();
                return response()->json($this->currentPosition);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur dans LogoController : ' . $e->getMessage());
            return response()->json(['error' => 'Commande non reconnue'], 400);
        }
    }
    private function getInitialTurtlePosition()
    {
        $canvasWidth = 500/* la largeur de votre canvas */;
        $canvasHeight = 500/* la hauteur de votre canvas */;
        // Logique pour obtenir la position initiale de la tortue
        $initialPosition = [
            'x' => $canvasWidth / 2, // Valeur initiale pour la coordonnée x
            'y' => $canvasHeight / 2, // Valeur initiale pour la coordonnée y
            'angle' => 0, // Valeur initiale pour l'angle
        ];

        return $initialPosition;
    }
    private function stopDrawing()
{
    $this->currentPosition['isDrawing'] = false;
    $this->setCurrentPosition($this->currentPosition);
}
    public function __construct()
    {
        if (session()->has('currentPosition')) {
            $this->currentPosition = session()->get('currentPosition');
        }
        else {
        $this->currentPosition = $this->getInitialTurtlePosition();
        session()->put('currentPosition', $this->currentPosition);
        }    
        }
    
        private function setCurrentPosition($currentPosition)
        {
            // Mettre à jour la session avec la nouvelle position
            session(['currentPosition' => $currentPosition]);
        }
        
        private function getCurrentPosition()
        {
            // Récupérer la position actuelle depuis la session
            return session('currentPosition', $this->getInitialTurtlePosition());
        }
    public function setAbsolutePosition($x, $y)
    {
        // Définir les nouvelles coordonnées absolues par rapport au centre
        $this->positionX = $x;
        $this->positionY = $y;
    
        // Retourner la position actuelle après la modification
        return [
            'x' => $this->positionX,
            'y' => $this->positionY,
            'angle' => $this->angle, // Assurez-vous que vous avez la propriété d'angle dans votre contrôleur
            // ... autres propriétés de position ...
        ];
    }
    private function getPosition()
{
    return $this->currentPosition;
}
    private function interpretWithMLLibrary($commands,$currentPosition)
    {
        $parts = explode(' ', $commands);

        // Obtenir la première partie (le nom de la commande)
        $action = strtoupper($parts[0]);
        // ... logique d'exécution ...
        $newPosition = $this->currentPosition;

    // Logique d'interprétation avec la bibliothèque ML
    $commands = [
         "AV" => "Avancer" ,
         "RE" => "Reculer",
         "TD" => "Tourner à Droite",
         "TG" => "Tourner à Gauche",
         "LC" => "La tortue ne laisse pas de trace",
         "BC" => "La tortue laisse une trace",
         "CT" => "La tortue est cachée",
         "MC" => "La tortue est visible",
         "VE" => "Efface l'ecran et replace la tortue au centre",
         "NETTOIE" => "Efface l'ecran sans changer la position de la tortue",
         "ORIGINE" => "Replace la tortue au centre",
         "VT" => "Efface la console",
         "FCC" => "Change la couleur du trait",
         "FCB" => "Change la couleur du fond",
         "FCAP" => "Fixe l'angle de la tortue",
         "CAP" => "Retourne l'angle de la tortue",
         "FPOS" => "Position de la tortue x,y",
         "POS" => "Retourne la position",
         'REPETE' => 'Répéter',
    ];
    if (array_key_exists($action, $commands)) {
    switch ($action) {
        case 'AV':
            // Logique pour avancer
            $distance = isset($parts[1]) ? intval($parts[1]) : 1; // Par défaut, avancer de 1 unité
            $newPosition = $this->moveForward($distance);
            break;

        case 'RE':
            // Logique pour reculer
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->moveBack($distance);
            break;
        case 'TD':
              // Logique pour tourner à droite
            $angle = isset($parts[1]) ? intval($parts[1]) : 90; // Par défaut, tourner de 45 ou 90 degrés selon penda
            $newPosition = $this->turnRight($angle);
            break;
        case 'TG':
            $angle = isset($parts[1]) ? intval($parts[1]) :90;
            $newPosition = $this->turnLeft($angle);
            break;
        case 'LC':
            $distance = isset($parts[1]) ? intval($parts[1]) : 1;
            $newPosition = $this->noTrack($newPosition, $currentPosition['angle']);
            break;
        
        case 'BC':
            $distance = isset($parts[1]) ? intval($parts[1]) : 1;
            $newPosition = $this->trackBack($newPosition, $currentPosition['angle']);
            break;
        case 'CT':
            // Logique pour cacher la tortue
            $interpretationResult = ['command' => 'CT'];
            break;
        
        case 'MC':
            // Logique pour montrer la tortue
            $interpretationResult = ['command' => 'MC'];
            break;
        case 'NETTOIE':
            $interpretationResult = ['command' => 'NETTOIE'];
            break;
        case 'VE':
            // Logique pour effacer l'écran et replacer la tortue au centre
            $interpretationResult = ['command' => 'VE'];
            $this->currentPosition = $this->getInitialTurtlePosition();
            break;
                
        case 'VT':
            $this->getInitialTurtlePosition();
            break;
        case 'ORIGINE':
            $newPosition = $this->clearScreenWithoutMoving($newPosition);
            break;
        case 'FCB':
            if (count($parts) >= 2) {
                $backgroundColor = $parts[1]; // Récupérez la couleur spécifiée dans la commande
                $newBackgroundColor = $this->changeBackgroundColor($backgroundColor);
            } else {
                // Gérez le cas où la commande ne contient pas suffisamment d'arguments
                return response()->json(['error' => 'La commande FCB nécessite une couleur en argument'], 400);
            }
            break;
        case 'FCC':
               // Logique pour changer la couleur du trait
               $red = isset($parts[1]) ? intval($parts[1]) : 0;
               $green = isset($parts[2]) ? intval($parts[2]) : 0;
               $blue = isset($parts[3]) ? intval($parts[3]) : 0;
           
               $newPosition = $this->changePenColor($red, $green, $blue);
               break;

        case 'FCAP':
            $angle =  $parts[1];
            $newPosition = $this->setAbsoluteAngle($angle);       
            break;
        case 'CAP':    
            $newPosition = $this->setAbsoluteAngle(90);
            break;
        case 'FPOS':
        // Logique pour positionner la tortue
        if (isset($parts[1]) && isset($parts[2])) {
            $newX = intval($parts[1]);
            $newY = intval($parts[2]);
            $newPosition = $this->setPosition($newX, $newY);
        }
        break;
        case 'POS':
                // Logique pour retourner la position de la tortue
                $newPosition = $this->getPosition();
        break;
        case 'REPETE':
            // Logique pour répéter un bloc d'instructions
            $repeatCount = isset($parts[1]) ? intval($parts[1]) : 1;
            $block = implode(' ', array_slice($parts, 2));
            $newPosition = $this->repeatBlock($block, $repeatCount, $currentPosition);
            break;

        default:
            // Cas par défaut pour les commandes non reconnues
            $newPosition = $currentPosition;  

        }
    } else { 
    // Retourner le résultat de l'interprétation
    return 'commande non reconnue';
    }
        return $newPosition;
  

    // Remplacer ceci par votre logique réelle
    
    }
     private function repeatBlock($block, $repeatCount, $currentPosition)
    {
        $newPosition = $currentPosition;

        for ($i = 0; $i < $repeatCount; $i++) {
            // Interprétez chaque commande dans le bloc
            $commandsInBlock = explode(' ', $block);
            foreach ($commandsInBlock as $command) {
                $newPosition = $this->interpretSingleCommand($command, $newPosition);
            }
        }

        return $newPosition;
    } 

      private function interpretSingleCommand($command, $currentPosition)
    {
        // Logique pour interpréter une seule commande
        $command = strtoupper(trim($command));

        // Logique pour interpréter une seule commande
        switch ($command) {
            case 'AV':
                $newPosition =  $this->moveForward(isset($parts[1]) ? intval($parts[1]) : 1);
                break;
            case 'RE':
                $newPosition =  $this->moveBack(isset($parts[1]) ? intval($parts[1]) : 1);
                break;
            case 'TD':
                $newPosition = $this->turnRight(isset($parts[1]) ? intval($parts[1]) : 0);
                break;
            case 'TG':
                $newPosition = $this->turnLeft(isset($parts[1]) ? intval($parts[1]) : 0);
                break;
            
            default:
                // Cas par défaut pour les commandes non reconnues
                $newPosition = $currentPosition;
                break;
        }

        return $newPosition;
    } 
     

    private function moveForward($distance)
    {
        $this->currentPosition = $this->getCurrentPosition();
    
        // Calculer les nouvelles coordonnées en fonction de l'angle et de la distance
        $angleInRadians = deg2rad($this->currentPosition['angle']);
        $deltaX = $distance * cos($angleInRadians);
        $deltaY = $distance * sin($angleInRadians);
    
        if (array_key_exists('x', $this->currentPosition)) {
            $this->currentPosition['x'] += $deltaX;
        }
    
        if (array_key_exists('y', $this->currentPosition)) {
            $this->currentPosition['y'] += $deltaY;
        }
        // Indiquer que la tortue est en train de dessiner
        $this->currentPosition['isDrawing'] = true;

        // Mise à jour de la position actuelle
        $this->setCurrentPosition($this->currentPosition);
    
        return $this->currentPosition;
    }

    private function moveBack($distance)
    {
        $this->currentPosition = $this->getCurrentPosition();
    
        // Calculer les nouvelles coordonnées en fonction de l'angle et de la distance
        $angleInRadians = deg2rad($this->currentPosition['angle']);
        $deltaX = $distance * cos($angleInRadians);
        $deltaY = $distance * sin($angleInRadians);
    
        if (array_key_exists('x', $this->currentPosition)) {
            $this->currentPosition['x'] -= $deltaX;
        }
    
        if (array_key_exists('y', $this->currentPosition)) {
            $this->currentPosition['y'] -= $deltaY;
        }
    
        // Mise à jour de la position actuelle
        $this->setCurrentPosition($this->currentPosition);
    
        return $this->currentPosition;
    }
    
    private function turnRight($angle)
{
    $this->currentPosition = $this->getCurrentPosition();

    // Calculer le nouvel angle en tournant à droite sur place
    $newAngle = ($this->currentPosition['angle'] + $angle) % 360;

    // Mettre à jour l'angle dans la nouvelle position
    $this->currentPosition['angle'] = $newAngle;

    // Mise à jour de la position actuelle
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;
}

private function turnLeft($angle)
{
    $this->currentPosition = $this->getCurrentPosition();

    // Calculer le nouvel angle en tournant à gauche sur place
    $newAngle = ($this->currentPosition['angle'] - $angle) % 360;

    // Mettre à jour l'angle dans la nouvelle position
    $this->currentPosition['angle'] = $newAngle;

    // Mise à jour de la position actuelle
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;
}

private function noTrack($currentPosition, $angle)
{
    // Utiliser la position actuelle passée en paramètre
    $newPosition = $currentPosition;

    // Marquer la tortue pour ne pas laisser de trace
    //$newPosition['isDrawing'] = false;

    return $newPosition;
}


private function trackBack($currentPosition, $angle)
{
    // Utiliser la position actuelle passée en paramètre
    $newPosition = $currentPosition;

    // Marquer la tortue pour laisser une trace
    //$newPosition['isDrawing'] = true;

    return $newPosition;
}

    private function clearScreen($currentPosition)
    {
        // Effacer l'écran en replaçant la tortue au centre
        $newPosition['x'] = 0;
        $newPosition['y'] = 0;
        $newPosition['angle'] = 0;
        $newPosition['isVisible'] = true;
    

        return $newPosition;
    }
    private function clearScreenAndMoveToCenter()
    {
        // Logique pour effacer l'écran
        $this->clearCanvas(); // Appel de la fonction pour effacer le canvas
    
        // Logique pour replacer la tortue au centre
        $newPosition = $this->moveToCenter($newPosition);
    
        return $newPosition;
    }
    private function hideTurtle()
{
    $this->isTurtleVisible = false;
}

private function showTurtle()
{
    $this->isTurtleVisible = true;
}

private function clearScreenWithoutMoving()
{
    // Logique pour effacer l'écran sans changer la position
    $this->clearCanvas(); // Appel de la fonction pour effacer le canvas sans changer la position

    // Aucune modification de position à effectuer ici

    return $this->currentPosition; // Ou retournez une réponse appropriée selon votre logique penda
}


private function clearCanvas()
{
    // Réinitialiser les positions précédentes
    $this->previousPositions = [];

    // Réinitialiser la position actuelle
    $this->currentPosition = $this->getInitialTurtlePosition();

    // Réinitialiser d'autres propriétés si nécessaire
    $this->isTurtleVisible = true; // Par exemple, remettre la tortue visible
    $this->penColor = "black"; // Réinitialiser la couleur du trait par défaut
}

private function moveToCenter($newPosition)
{
    $canvasWidth = 500; // Ajustez la valeur en conséquence
    $canvasHeight = 500; // Ajustez la valeur en conséquence

    // Logique pour replacer la tortue au centre
    $newPosition['x'] = $this->canvasWidth / 2;
    $newPosition['y'] = $this->canvasHeight / 2;
    $newPosition['angle'] = 0; 
    $newPosition['isVisible'] = true;
 

    return $newPosition;
}
private function setPosition($newX, $newY)
{
    // Mettez à jour les coordonnées de la position actuelle
    $this->currentPosition['x'] = $newX;
    $this->currentPosition['y'] = $newY;
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;


}
    private function changeLineColor($color, $currentPosition)
    {
        // Changer la couleur du trait
        $newPosition['lineColor'] = $color;

        // Copier les autres indicateurs sans les modifier
        $newPosition['x'] = $currentPosition['x'];
        $newPosition['y'] = $currentPosition['y'];
        $newPosition['angle'] = $currentPosition['angle'];
      
        $newPosition['isVisible'] = $currentPosition['isVisible'];

        return $newPosition;
    }

    private function getBackgroundColor($color)
    {
        // Vérifiez d'abord si la clé 'backgroundColor' existe dans le tableau
        if (array_key_exists('backgroundColor', $color)) {
            return $color['backgroundColor'];
        }

        // Par défaut, retournez une valeur par défaut ou lancez une exception selon vos besoins
        return '#RRGGBB'; // Valeur par défaut blanche, remplacez-la par ce que vous préférez
    }

    // Utilisation dans votre méthode changeBackgroundColor
    private function changeBackgroundColor($backgroundColor)
    {
        // Implement logic to validate and set the new background color
        $newBackgroundColor = $backgroundColor; // Replace this with your validation logic
    
        // Additional logic if needed
    
        // Return the updated position or other relevant information
        return [
            'backgroundColor' => $newBackgroundColor,
        ];
    }

    private function changePenColor($red, $green, $blue)
    {
        $this->currentPosition['penColor'] = "rgb($red, $green, $blue)";
        $this->setCurrentPosition($this->currentPosition);

        return $this->currentPosition;


    }
    private function setPenColor($color, $currentPosition)
    {
        $newPosition['lineColor'] = $color;

        // Copier les autres indicateurs sans les modifier
        $newPosition['x'] = $currentPosition['x'];
        $newPosition['y'] = $currentPosition['y'];
        $newPosition['angle'] = $currentPosition['angle'];
        $newPosition['isVisible'] = true;
    
        // Retourner la position actuelle de la tortue ou d'autres données nécessaires
        return $newPosition;
    }

    private function setAbsoluteAngle($angle)
    {
    // Assurez-vous que l'angle est dans la plage [0, 360)
    $angle = ($angle + 360) % 360;

    // Mise à jour de l'angle dans la position actuelle
    $this->currentPosition['angle'] = $angle;

    // S'assurer que les autres propriétés de position restent inchangées
    $this->setCurrentPosition($this->currentPosition);

    // Retourner la position mise à jour
    return $this->currentPosition;
    }

    private $userProcedures = [];

    public function interpret($commands)
    {
        $parts = explode(' ', $commands);
        $action = strtoupper($parts[0]);

        switch ($action) {
            case 'POUR':
                $this->defineProcedure($parts);
                break;
            default:
                $this->executeProcedure($action, $parts);
                break;
        }
    }

    private function defineProcedure($parts)
    {
        $procedureName = $parts[1];
        $params = array_slice($parts, 3, -1); // Les paramètres de la procédure
        $instructions = implode(' ', array_slice($parts, 2)); // Les instructions de la procédure

        $this->userProcedures[$procedureName] = [
            'params' => $params,
            'instructions' => $instructions,
        ];
    }

    private function executeProcedure($procedureName, $params)
    {
        if (array_key_exists($procedureName, $this->userProcedures)) {
            $procedure = $this->userProcedures[$procedureName];

            // Créer un tableau associatif de paramètres
            $paramValues = [];
            foreach ($procedure['params'] as $index => $paramName) {
                $paramValues[$paramName] = isset($params[$index + 1]) ? intval($params[$index + 1]) : 0;
            }

            // Remplacer les paramètres dans les instructions
            $instructions = $procedure['instructions'];
            foreach ($paramValues as $paramName => $paramValue) {
                $instructions = str_replace(":$paramName", $paramValue, $instructions);
            }

            // Interpréter les instructions
            $this->interpret($instructions);
        } else {
            // Procédure non définie
            echo "Erreur: Procédure '$procedureName' non définie\n";
        }    
    }

    private function interpretRecursive($instructions)
    {
        $parts = explode(' ', $instructions);

        foreach ($parts as $part) {
            // Ignorer les espaces vides
            if (!empty($part)) {
                // Interpréter chaque commande, y compris les procédures appelées récursivement
                $this->interpret($part);
            }
        }
    }
}
