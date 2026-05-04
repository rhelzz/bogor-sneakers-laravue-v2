import { ref } from 'vue';

export function useStudioHistory() {
    const history = ref<string[]>([]);
    const redoStack = ref<string[]>([]);
    const isRestoring = ref(false);

    const pushState = (state: any) => {
        if (isRestoring.value) return;

        const stateStr = JSON.stringify(state);
        
        // Don't push if state is identical to last
        if (history.value.length > 0 && history.value[history.value.length - 1] === stateStr) {
            return;
        }

        history.value.push(stateStr);
        if (history.value.length > 50) {
            history.value.shift();
        }
        redoStack.value = [];
    };

    const undo = () => {
        if (history.value.length <= 1) return null;

        const currentState = history.value.pop()!;
        redoStack.value.push(currentState);

        return JSON.parse(history.value[history.value.length - 1]);
    };

    const redo = () => {
        if (redoStack.value.length === 0) return null;

        const nextState = redoStack.value.pop()!;
        history.value.push(nextState);

        return JSON.parse(nextState);
    };

    const clearHistory = () => {
        history.value = [];
        redoStack.value = [];
    };

    return {
        history,
        redoStack,
        isRestoring,
        pushState,
        undo,
        redo,
        clearHistory
    };
}
