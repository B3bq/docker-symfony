/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function() {
        const questionsWrapper = document.querySelector('.questions-wrapper');
        const addQuestionBtn = document.querySelector('.add-question');

        // Funkcja do inicjalizacji przycisków usuwania i dodawania dla istniejących elementów
        function initElements(container) {
            container.querySelectorAll('.remove-question').forEach(btn => {
                btn.onclick = function() { this.closest('.question-item').remove(); };
            });
            container.querySelectorAll('.remove-answer').forEach(btn => {
                btn.onclick = function() { this.closest('.answer-item').remove(); };
            });
            container.querySelectorAll('.add-answer').forEach(btn => {
                btn.onclick = function() {
                    const wrapper = this.previousElementSibling;
                    let prototype = wrapper.dataset.prototype;
                    let index = wrapper.dataset.index;
                    let newForm = prototype.replace(/__name__/g, index);
                    wrapper.dataset.index = parseInt(index) + 1;

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = newForm;
                    const protoNode = tempDiv.firstElementChild;
                    if (!protoNode) return;

                    const questionIndex = wrapper.dataset.questionIndex || wrapper.closest('.question-item')?.dataset.questionIndex || '0';

                    // Znajdowanie inputow w prototype
                    const checkbox = protoNode.querySelector('input[type="checkbox"]');
                    const hiddenInput = checkbox ? protoNode.querySelector(`input[type="hidden"][name="${checkbox.name}"]`) : null;
                    const textInput = protoNode.querySelector('input[type="text"]');

                    // dodanie klasy answer-item do wrappera odpowiedzi, jeśli nie istnieje
                    let answerItem;
                    if (protoNode.classList.contains('answer-item')) {
                        answerItem = protoNode;
                    } else {
                        answerItem = document.createElement('div');
                        answerItem.className = 'input-group mb-2 answer-item';
                        answerItem.appendChild(protoNode);
                    }

                    // schowanie checkboxa, aby nie był widoczny, ale nadal obecny w DOM
                    if (checkbox) checkbox.style.display = 'none';

                    // dodanie radio buttona do wyboru poprawnej odpowiedzi
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `correct_answer_${questionIndex}`;
                    radio.className = 'correct-answer-radio';
                    radio.dataset.questionIndex = questionIndex;

                    // dodanie labela "Poprawna" obok radio buttona
                    const textGroup = document.createElement('div');
                    textGroup.className = 'input-group-text bg-light';
                    textGroup.appendChild(radio);
                    if (hiddenInput) textGroup.appendChild(hiddenInput.cloneNode(true));
                    if (checkbox) textGroup.appendChild(checkbox);
                    // unikanie duplikatu labela
                    let movedLabel = null;
                    if (checkbox) {
                        movedLabel = protoNode.querySelector(`label[for="${checkbox.id}"]`);
                    }
                    if (movedLabel) {
                        textGroup.appendChild(movedLabel);
                    } else {
                        const label = document.createElement('label');
                        label.className = 'ms-2 mb-0';
                        label.textContent = 'Poprawna';
                        textGroup.appendChild(label);
                    }

                    // dodanie textGroup na początek answerItem
                    answerItem.insertBefore(textGroup, answerItem.firstChild);

                    // dodanie przycisku usuwania odpowiedzi
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-outline-danger remove-answer';
                    removeBtn.textContent = 'X';
                    answerItem.appendChild(removeBtn);
                    removeBtn.onclick = function() { this.closest('.answer-item').remove(); };

                    // sprawdzenie, czy textInput istnieje i dodanie klasy form-control
                    if (textInput) textInput.classList.add('form-control');

                    wrapper.appendChild(answerItem);
                };
            });
        }

        initElements(questionsWrapper);

        // sprawdzenie i ustawienie name dla radio buttonów poprawnej odpowiedzi
        document.querySelectorAll('.answers-wrapper').forEach(wrapper => {
            const qIdx = wrapper.dataset.questionIndex || Array.from(document.querySelectorAll('.question-item')).indexOf(wrapper.closest('.question-item'));
            wrapper.querySelectorAll('input.correct-answer-radio').forEach(radio => {
                radio.name = `correct_answer_${qIdx}`;
                radio.dataset.questionIndex = qIdx;
            });
        });

        // Dodawanie nowych pytań
        addQuestionBtn.addEventListener('click', function() {
            let prototype = questionsWrapper.dataset.prototype;
            let index = questionsWrapper.dataset.index;
            let newForm = prototype.replace(/__name__/g, index);
            questionsWrapper.dataset.index = parseInt(index) + 1;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newForm;
            const contentInput = tempDiv.querySelector('input[type="text"]');
            
            const answersPrototypeDiv = tempDiv.querySelector('div[data-prototype]');
            let answersPrototype = answersPrototypeDiv ? answersPrototypeDiv.dataset.prototype : '';

            const questionHtml = `
                <div class="card mb-4 border-primary question-item">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <strong>Nowe Pytanie</strong>
                        <button type="button" class="btn btn-sm btn-danger remove-question">Usuń pytanie</button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Treść pytania</label>
                            ${contentInput.outerHTML}
                        </div>
                        <h6 class="text-muted mb-3">Warianty odpowiedzi:</h6>
                        <div class="answers-wrapper" data-prototype="${answersPrototype.replace(/"/g, '&quot;')}" data-index="0" data-question-index="${index}">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-answer">+ Dodaj wariant</button>
                    </div>
                </div>
            `;

            questionsWrapper.insertAdjacentHTML('beforeend', questionHtml);
            
            const newlyAdded = questionsWrapper.lastElementChild;
            initElements(newlyAdded);
            
            // Od razu dodaj 3 puste odpowiedzi dla nowego pytania
            const addAnswerBtn = newlyAdded.querySelector('.add-answer');
            if(addAnswerBtn) {
                addAnswerBtn.click();
                addAnswerBtn.click();
                addAnswerBtn.click();
            }
        });
    });

    // zanim formularz zostanie wysłany, poprawiamy nazwy pól, aby Symfony je poprawnie zmapowało
    document.querySelector('form').addEventListener('submit', function(e) {
        let questionIndex = 0;
        document.querySelectorAll('.question-item').forEach((qItem) => {
            // znalezienie pytania
            let contentInput = null;
            qItem.querySelectorAll('input[type="text"]').forEach(inp => {
                if (!inp.name || !inp.name.includes('answer')) {
                    contentInput = inp;
                }
            });
            if (contentInput) {
                contentInput.name = `survey[questions][${questionIndex}][content]`;
            }

            // naprawa nazw pól odpowiedzi
            let answerIndex = 0;
            qItem.querySelectorAll('.answer-item').forEach((aItem) => {
                // input treści odpowiedzi
                const textInput = aItem.querySelector('input[type="text"][placeholder*="odpowiedzi"]') || 
                                 Array.from(aItem.querySelectorAll('input[type="text"]')).find(inp => inp !== contentInput);
                if (textInput) {
                    textInput.name = `survey[questions][${questionIndex}][answers][${answerIndex}][content]`;
                }

                // checkbox poprawnej odpowiedzi
                const checkbox = aItem.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.name = `survey[questions][${questionIndex}][answers][${answerIndex}][isCorrect]`;
                }

                // naprawa ukrytego inputa, który Symfony używa do mapowania checkboxów
                const hiddenInput = aItem.querySelector(`input[type="hidden"][value="0"]`);
                if (hiddenInput && hiddenInput.dataset.symfony) {
                    hiddenInput.name = `survey[questions][${questionIndex}][answers][${answerIndex}][isCorrect]`;
                } else if (hiddenInput && !hiddenInput.name.includes(`questions`)) {
                    hiddenInput.name = `survey[questions][${questionIndex}][answers][${answerIndex}][isCorrect]`;
                }

                answerIndex++;
            });

            questionIndex++;
        });
    });

    document.addEventListener('change', function (e) {
    if (e.target.classList.contains('correct-answer-radio')) {
        const name = e.target.name;
        document.querySelectorAll(`input.correct-answer-radio[name="${name}"]`).forEach((radio) => {
            const checkbox = radio.parentElement.querySelector('input[type="checkbox"]');
            if (checkbox) checkbox.checked = false;
        });

        const checkbox = e.target.parentElement.querySelector('input[type="checkbox"]');
        if (checkbox) checkbox.checked = true;
    }
    });
