const elementsArray = [
    { label: "Label 1", value: "Value 1" },
    { label: "Label 2", value: "Value 2" },
    { label: "Label 3", value: "Value 3" },
    { label: "Label 3", value: "Value 3" },
];
function openDialog() {
   
            app.dialog.create({
                
                title: 'Modifier l’entreprise',
                content: [
                    
                    app.createElement('label', { for: 'entreprise', style: { display: 'block', } }, 'Nom de l\'entreprise'),
                     
                    app.createElement('div',{
                        style : {
                         
                            display :'flex',
                            border: '3px solid #85D3FF',
                            borderRadius: '10px',
                            overflow: 'hidden',
                            height : '40px',
                            width:'100%',
                            marginBottom: '20px',
                        }
                    },
                    app.createElement('img', {
                        src : '//static.projet-web.fr/public/img/Organization.svg'
                     }, 'Titre'),
                       app.createElement('input',{
                        Name: 'entreprise',
                            Placeholder : "Nom de l'entreprise",
                            style : {
                                border : 'none',
                                
                            }
                        }, 'Titre')),


                    app.createElement('div',{style:{display: 'grid', /* Utilisation de CSS Grid */
                    gridTemplateColumns: '70% 20%',/* Crée deux colonnes de taille automatique */
                    }},  
                     app.createElement('h3', { }, "Liste des adresses",),  
                    app.createElement('button', {  }, 'Ajouter +'),),
                     



                    ...elementsArray.map(element => {
                       return app.createElement('div', { style:{display:'flex',justifyContent: 'space-between',}}, "",  
                        app.createElement('div',{
                       style : {
                           display :'flex',
                           borderRadius: '10px',
                           overflow: 'hidden',
                           height : '40px',
                           borderBottom: '2px solid #85D3FF',
                           width:'60%',
                           marginRight: '15px',
                       }
                   },
                  
                       app.createElement('input', {
                           value : "12 Rue des Cramptés",
                           style : {
                               border : 'none',
                           }
                       }, 'Titre')),
                       app.createElement('div',{
                       style : {
                           display :'flex',
                           borderRadius: '10px',
                           overflow: 'hidden',
                           height : '40px',
                           borderBottom: '2px solid #85D3FF',
                           width:'15%',
                           marginRight: '15px',
                        
                       }
                   },

                       app.createElement('input', {
                           value : "45700",
                           style : {
                               border : 'none',

                           }
                       }, 'Titre')),
                       app.createElement('div',{
                       style : {
                           display :'flex',
                           borderRadius: '10px',
                           overflow: 'hidden',
                           height : '40px',
                           borderBottom: '2px solid #85D3FF',
                           width:'15%',
                           marginRight: '15px',
                       }
                   },

                       app.createElement('input', {
                           value : "la source ",
                           style : {
                               border : 'none',

                           }
                       }, 'Titre')),
                       
                       app.createElement('div',{
                       style : {
                           display :'flex',
                           borderRadius: '10px',
                           overflow: 'hidden',
                           height : '40px',
                           borderBottom: '2px solid #85D3FF',
                           width:'15%'
                       }
                   },

                       app.createElement('input', {
                           value : "France ",
                           style : {
                               border : 'none',

                           }
                       }, 'Titre')),
                       app.createElement('a', {
                        href:"page-cible.html",
                        style:{
                            marginLeft: '15px',
                        }
                    
                 },  app.createElement('img', {
                    src : '//static.projet-web.fr/public/img/trash.svg'
                 }, 'Titre'),),)
                    }),



          

                    
                ],
                buttons: [
                    { text: 'Annuler', action: 'close' },
                    { text: 'Valider', action: (close) => { close(); } }
                ]
            }).show();
        }


