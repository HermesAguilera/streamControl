document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los placeholders de avatar
    const avatarPlaceholders = document.querySelectorAll('.avatar-placeholder');
    
    // Para cada placeholder
    avatarPlaceholders.forEach(placeholder => {
        // Limpiar el contenido actual (que podría mostrar HTML como texto)
        placeholder.innerHTML = '';
        
        // Verificar si tiene imagen
        const hasImage = placeholder.getAttribute('data-has-image') === 'true';
        const imagePath = placeholder.getAttribute('data-image-path');
        
        if (hasImage && imagePath) {
            // Crear elemento de imagen
            const img = document.createElement('img');
            img.src = imagePath;
            img.style.width = '120px';
            img.style.height = '120px';
            img.style.borderRadius = '50%';
            img.style.objectFit = 'cover';
            placeholder.appendChild(img);
        } else {
            // Crear elemento sin foto
            const noPhotoDiv = document.createElement('div');
            noPhotoDiv.style.width = '120px';
            noPhotoDiv.style.height = '120px';
            noPhotoDiv.style.borderRadius = '50%';
            noPhotoDiv.style.background = '#eee';
            noPhotoDiv.style.display = 'flex';
            noPhotoDiv.style.alignItems = 'center';
            noPhotoDiv.style.justifyContent = 'center';
            noPhotoDiv.style.fontSize = '16px';
            noPhotoDiv.style.color = '#888';
            noPhotoDiv.style.margin = '0 auto';
            noPhotoDiv.textContent = 'Sin foto';
            placeholder.appendChild(noPhotoDiv);
        }
    });
});
