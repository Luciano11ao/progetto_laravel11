<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::query()
            ->where('user_id', request()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();
        return view('note.index', ['notes' => $notes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('note.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'note' => ['required', 'string']
        ]);
        $data['user_id'] = $request->user()->id;
        $note = Note::create($data);
        
        ActionLog::create([
            'note_id' => $note->id,
            'user_id' => $request->user()->id,
            'action_type' => 'create',
        ]);
        
        return to_route('note.show', $note)->with('message', 'Note was created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        // Ottieni l'ID dell'utente autenticato
        $userId = request()->user()->id;

        // Log dell'ID dell'utente
        Log::info("ID dell'utente: $userId");

        // Verifica se l'utente è autorizzato a visualizzare la nota
        if ($note->user_id !== $userId) {
            abort(403);
        }

        // Passa la nota alla vista
        return view('note.show', ['note' => $note]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        if ($note->user_id !== request()->user()->id) {
            abort(403);
        }
        return view('note.edit', ['note' => $note]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'note' => ['required', 'string']
        ]);
        $note->update($data);

        ActionLog::create([
            'note_id' => $note->id,
            'user_id' => $request->user()->id,
            'action_type' => 'edit',
        ]);

        return to_route('note.show', $note)->with('message', 'Note was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
{
    // Verifica l'autorizzazione dell'utente
    if ($note->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Elimina la nota
    $note->delete();

    // Restituisci una risposta JSON per confermare l'eliminazione
    return response()->json(['message' => 'Nota eliminata con successo']);
}

    
}
