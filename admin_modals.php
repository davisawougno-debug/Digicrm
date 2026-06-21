<!-- Modal de confirmation de suppression -->
<div class="modal" id="deleteModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal--sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmer la suppression</h3>
                <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteModalMessage">Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn--outline" onclick="closeModal('deleteModal')">Annuler</button>
                <a href="#" id="deleteModalConfirm" class="btn btn--danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal générique -->
<div class="modal" id="genericModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="genericModalTitle">Titre</h3>
                <button class="modal-close" onclick="closeModal('genericModal')">&times;</button>
            </div>
            <div class="modal-body" id="genericModalBody">
            </div>
        </div>
    </div>
</div>
