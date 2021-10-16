import VuexORM from '@vuex-orm/core'
import _ from 'lodash'
import pluralize from 'pluralize'

// Create new instance of Database.
const database = new VuexORM.Database()

// Register Model and Module
// Recursive find files in store-entities directory
const entityFiles = require.context(
  '@/store-entities',
  false,
  /^\.\/(?!-)[^.]+\.(js)$/
)
const modelFiles = require.context(
  '@/store-entities/models',
  false,
  /^\.\/(?!-)[^.]+\.(js)$/
)
const entityFileNames = entityFiles.keys()
const modelFileNames = modelFiles.keys()

modelFileNames.forEach((modelFileName) => {
  const entityName = getEntityNameFromModel(modelFileName)
  const entityFileName = `./${entityName}.js`

  // Check that the corresponding store entity exist
  if (!_.includes(entityFileNames, entityFileName)) {
    throw new Error(
      `Cannot find corresponding store entity for model [${modelFileName}]. Was searching for [${entityFileName}]`
    )
  }

  // Finally, register them
  database.register(
    modelFiles(modelFileName).default,
    entityFiles(entityFileName).default
  )
})

function getEntityNameFromModel (modelFileName) {
  const modelName = modelFileName.replace(/^\.\//, '').replace(/\.(js)$/, '')
  return lowercaseFirstLetter(pluralize(modelName))
}

function lowercaseFirstLetter (string) {
  return string.charAt(0).toLowerCase() + string.slice(1)
}

export const plugins = [VuexORM.install(database)]

export const state = () => ({
  title: 'FinancialApp',
  isMenuHidden: false,
  isMainNavOpen: false
})

export const mutations = {
  setTitle (state, title) {
    state.title = title
  },
  setIsMenuHidden (state, isMenuHidden) {
    state.isMenuHidden = isMenuHidden
  },
  setIsMainNavOpen (state, isMainNavOpen) {
    state.isMainNavOpen = isMainNavOpen
  }
}

export const actions = {
  setTitle (context, title) {
    context.commit('setTitle', title)
  },
  setIsMenuHidden (context, isMenuHidden) {
    context.commit('setIsMenuHidden', isMenuHidden)
  },
  setIsMainNavOpen (context, isMainNavOpen) {
    context.commit('setIsMainNavOpen', isMainNavOpen)
  }
}
