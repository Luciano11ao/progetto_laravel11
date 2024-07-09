<?php

namespace App\Http\Controllers\API\Note;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\Validator;
use League\Csv\Exception as CsvException;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;

class APINoteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); // Recupera l'utente autenticato
        
        // Recupera le note dell'utente autenticato
        $notes = Note::where('user_id', $user->id)
                     ->orderBy('created_at', 'desc')
                     ->get();

        // Restituisce le note in formato JSON
        return response()->json($notes);
    }

    public function store(Request $request)
{
    try {
        Log::info('Store function started');

        Log::info('Request data:', $request->all());

        // Validazione dei dati ricevuti, inclusa l'immagine
        $validatedData = $request->validate([
            'note' => 'required|string',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        Log::info('Validation passed');

        // Recupera l'utente autenticato
        $user = Auth::user();
        Log::info('Authenticated user: ', ['user_id' => $user->id]);

        // Creare una nuova nota associata all'utente autenticato
        $note = new Note();
        $note->note = $validatedData['note'];
        $note->user_id = $user->id;

        // Caricamento dell'immagine se è stata fornita
        if ($request->hasFile('image')) {
            Log::info('Image file is present');

            // Ottieni il file dell'immagine
            $image = $request->file('image');
            
            // Crea un nome unico per l'immagine
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Definisci il percorso in cui verrà salvata l'immagine
            $imagePath = 'images/' . $imageName;

            // Sposta l'immagine nella directory pubblica
            $success = $image->move(public_path('images'), $imageName);

            if ($success) {
                Log::info('Image moved to public/images', ['image_path' => $imagePath]);

                // Salva il percorso dell'immagine nel database
                $note->image = $imagePath;
            } else {
                Log::error('Failed to move image');
            }
        } else {
            Log::info('No image file present');
        }

        // Salva la nota nel database
        $note->save();

        Log::info('Note saved', ['note_id' => $note->id]);

        // Registrare l'azione di creazione nella tabella ActionLog
        ActionLog::create([
            'note_id' => $note->id,
            'user_id' => $user->id,
            'action_type' => 'create',
        ]);

        Log::info('ActionLog created');

        // Ritorna una risposta JSON con la nota creata
        return response()->json($note, 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed: ' . $e->getMessage());
        return response()->json(['error' => 'Errore di validazione: ' . $e->getMessage()], 422);
    } catch (\Exception $e) {
        Log::error('Error storing note: ' . $e->getMessage());
        return response()->json(['error' => 'Errore durante il salvataggio della nota'], 500);
    }
}   

public function update(Request $request, Note $note)
    {
        // Debug per verificare se la nota è stata correttamente recuperata
        info($note); // Assicurati che $note sia un'istanza di Note e non un array vuoto
        info($note->user_id);
        info($request->user()->id);

        // Verifica se l'utente corrente è autorizzato ad aggiornare la nota
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Esegui l'aggiornamento della nota
        $data = $request->validate([
            'note' => ['required', 'string']
            // Altri campi da validare e aggiornare
        ]);

        $note->update($data);

        // Log dell'azione di aggiornamento
        Log::info("Nota aggiornata: $note->id");

        ActionLog::create([
            'note_id' => $note->id,
            'user_id' => $request->user()->id,
            'action_type' => 'edit',
        ]);

        // Restituisci una risposta JSON per confermare l'aggiornamento
        return response()->json(['message' => 'Nota aggiornata con successo', 'note' => $note]);
    }


    public function destroy(Note $note)
    {
        // Verifica se l'utente corrente è autorizzato a eliminare la nota
        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Elimina la nota
        $note->delete();

        // Restituisci una risposta JSON per confermare l'eliminazione
        return response()->json(['message' => 'Nota eliminata con successo']);
    }

    
public function importNotes(Request $request)
{
    // Validazione del file CSV
    $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:csv,txt',
    ]);

    // Se la validazione fallisce, restituisci un errore
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first()], 400);
    }

    // Recupera il file dalla richiesta
    $file = $request->file('file');
    $filePath = $file->getPathname();

    try {
        // Lettore per CSV
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); // Prima riga contiene i nomi delle colonne
        $csv->setDelimiter(';'); // Imposta il separatore dei campi a punto e virgola

        // Prendo tutti i record dal file CSV
        $records = $csv->getRecords();

        // Log per verificare i record letti
        Log::info('Record CSV letti', ['records' => iterator_to_array($records)]);

        // Itero sui record e crea le nuove note nel database
        foreach ($records as $record) {
            Log::info('Elaborazione record CSV', ['record' => $record]);

            // Cerco l'utente per user_id
            $user = User::find($record['user_id']);
            if ($user) {
                Log::info('Utente trovato', ['user_id' => $user->id]);

                Note::create([
                    'note' => $record['testo'],
                    'user_id' => $user->id,
                ]);
            } else {
                Log::info('Sono dentro l\'if: utente non trovato', ['user_id' => $record['user_id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'utente/utenti non trovati: ' . $record['user_id']
                ]);
            }
        }

        // Restituisce una risposta di successo
        return response()->json(['message' => 'Importazione completata con successo']);

    } catch (\Exception $e) {
        // Log dell'errore per un controllo più dettagliato
        Log::error('Errore durante l\'importazione del file CSV: ' . $e->getMessage());

        // Restituisci una risposta JSON con un messaggio di errore generico
        return response()->json(['error' => 'Errore durante l\'importazione dei dati'], 500);
    }
}
public function importNotesExcel(Request $request)
{
    // Validazione del file Excel
    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:xlsx,xls', // controllo dell'estensione giusta
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first()], 400);
    }

    Log::info('Inizio importazione del file Excel');

    try {
        // recupero il file inviato con la richiesta HTTP
        $file = $request->file('file');

        // Importo il file Excel utilizzando Maatwebsite/Laravel-Excel
        Excel::import(new class implements ToModel { //toModel è un'interfaccia di maatwebsite che consente di mappare i dati di ogni riga del file excel a un modello Eloquent.
            public function model(array $row) // la funzione model riceve un array che rappresenta i dati di una riga del file, indice 0 è testo e indice 1 è la colonna dello user_id.
            {
                Log::info('Elaborazione riga Excel', ['row' => $row]);

                // Trovo l'utente per user_id
                $user = User::find($row[1]); // Indice 1 perché user_id è nella seconda colonna

                if ($user) { // se l'utente viene trovato viene creata una istanza nuova di Note con i dati giusti, sennò non viene proprio creata.
                    return new Note([
                        'note' => $row[0], // testo
                        'user_id' => $user->id,
                    ]);
                } else {
                    Log::info('Utente non trovato per user_id: ' . $row[1]);
                    return null; //se non trova l'utente non crea la nota
                }
            }
        }, $file);

        Log::info('Fine importazione del file Excel');

        // Restituisce una risposta di successo
        return response()->json(['message' => 'Importazione completata con successo']);

    } catch (\Exception $e) {
        Log::error('Errore durante l\'importazione del file Excel: ' . $e->getMessage());
        // Gestione degli errori durante l'importazione
        return response()->json(['error' => 'Errore durante l\'importazione dei dati'], 500);
    }
}
}
