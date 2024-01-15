// Function to perform a deep clone of an object or array
export const deepClone = function(obj) {
    // Base case: if obj is null or not of type 'object', return obj as it is
    if (obj === null || typeof obj !== 'object') {
      return obj;
    }
    
    // Create a new copy, checking if obj is an array or object
    const copy = Array.isArray(obj) ? [] : {};
    
    // Iterate over all properties of the obj
    for (let key in obj) {
      // Recursively deep clone each property and assign it to the copy
      copy[key] = deepClone(obj[key]);
    }
  
    // Return the cloned object or array
    return copy;
}
  