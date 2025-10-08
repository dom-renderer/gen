<form id="form-section-g-2">

    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <div class="row mb-3 align-items-center">
                <div class="col-12">
                    <label for="sg2-date" class="fw-bold"> Date of Note  </label>
                </div>
                <div class="col-12">
                    <input type="text" name="noted_at" id="sg2-date" class="form-control" placeholder="YY-MM-DD" readonly >
                </div>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="row mb-3 align-items-center">
                <div class="col-12">
                    <label for="sg2-notedby" class="fw-bold "> Note By   </label>
                </div>
                <div class="col-12">
                    <input type="text" name="noted_by" id="sg2-notedby" class="form-control" >
                </div>
            </div>
        </div>
    </div>

   
    <div class="row mb-3 align-items-center">
        <div class="col-12">
            <label for="sg2-note" class="fw-bold"> Note(s)  </label>
        </div>
        <div class="col-12">
            <textarea name="note" id="sg2-note" class="form-control" rows="10" ></textarea>
        </div>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        <button type="submit" data-type="save-and-add" class="btn btn btn-primary">Save & Add New</button>
        <button type="submit" data-type="next" data-next="section-g-2" data-final="yes" class="btn btn-primary save-next">Save</button>
        @endif
    </div>
</form>

<div id="case-file-notes-container" style="margin-top: 100px;">
    @if($g2Data && $g2Data->count() > 0)
        <div class="mb-4">
            <h5>Previous Case File Notes</h5>
            <div class="accordion" id="caseFileNotesAccordion">
                @foreach($g2Data as $index => $note)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                {{ $note->noted_by ?: 'Note' }} - {{ $note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y') : 'No Date' }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#caseFileNotesAccordion">
                            <div class="accordion-body">
                                <div class="row mb-2">
                                    <div class="col-sm-3"><strong>Date of Note: </strong></div>
                                    <div class="col-sm-9">{{ $note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y H:i') : 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3"><strong>Note By: </strong></div>
                                    <div class="col-sm-9">{{ $note->noted_by ?: 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3"><strong>Note(s): </strong></div>
                                    <div class="col-sm-9">{{ $note->notes ?: 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="deleteCaseFileNote({{ $note->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>